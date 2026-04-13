<?php

namespace Tests\Feature\Services;

use App\Models\Prize;
use App\Models\PrizeRedemption;
use App\Models\Score;
use App\Models\User;
use App\Services\PrizeRedemptionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class PrizeRedemptionServiceTest extends TestCase
{
    use RefreshDatabase;

    private function makeService(): PrizeRedemptionService
    {
        return app(PrizeRedemptionService::class);
    }

    // =========================================================================
    // 1. Happy path — redemption record created, score consumed
    // =========================================================================

    public function test_redeem_happy_path_creates_redemption_and_consumes_scores(): void
    {
        $user  = User::factory()->create();
        $score = Score::factory()->create(['user_id' => $user->id, 'amount' => 200, 'status' => 1]);
        $prize = Prize::factory()->create(['cost' => 100, 'status' => 1]);

        $redemption = $this->makeService()->redeem($user, $prize->id);

        $this->assertInstanceOf(PrizeRedemption::class, $redemption);

        $this->assertDatabaseHas('prize_redemptions', [
            'user_id'  => $user->id,
            'prize_id' => $prize->id,
            'status'   => 'approved',
        ]);

        // Score consumed (whole 200pt score marked spent because 200 >= 100)
        $this->assertSame(0, $score->fresh()->status);
    }

    // =========================================================================
    // 2. FIFO order — oldest score consumed first
    // =========================================================================

    public function test_redeem_consumes_oldest_scores_first(): void
    {
        $user = User::factory()->create();

        // Three 50pt scores, distinct ages
        $oldest = Score::factory()->create([
            'user_id'    => $user->id,
            'amount'     => 50,
            'status'     => 1,
            'created_at' => now()->subMinutes(30),
        ]);
        $middle = Score::factory()->create([
            'user_id'    => $user->id,
            'amount'     => 50,
            'status'     => 1,
            'created_at' => now()->subMinutes(15),
        ]);
        $newest = Score::factory()->create([
            'user_id'    => $user->id,
            'amount'     => 50,
            'status'     => 1,
            'created_at' => now(),
        ]);

        // Prize costs 60 — needs oldest (50pt, remaining=10) then middle (50pt, remaining=-40 → stop)
        $prize = Prize::factory()->create(['cost' => 60, 'status' => 1]);

        $this->makeService()->redeem($user, $prize->id);

        $this->assertSame(0, $oldest->fresh()->status, 'Oldest score should be consumed');
        $this->assertSame(0, $middle->fresh()->status, 'Middle score should be consumed (over-consumption)');
        $this->assertSame(1, $newest->fresh()->status, 'Newest score should remain active');
    }

    // =========================================================================
    // 3. Exact cost match — 1 score of exactly prize cost marked status=0
    // =========================================================================

    public function test_redeem_exact_cost_marks_single_score_as_used(): void
    {
        $user  = User::factory()->create();
        $score = Score::factory()->create(['user_id' => $user->id, 'amount' => 100, 'status' => 1]);
        $prize = Prize::factory()->create(['cost' => 100, 'status' => 1]);

        $this->makeService()->redeem($user, $prize->id);

        $this->assertSame(0, $score->fresh()->status);
        $this->assertSame(1, PrizeRedemption::where('user_id', $user->id)->count());
    }

    // =========================================================================
    // 4. Over-consumption — 100pt score, 60pt prize → whole 100pt score marked status=0
    // =========================================================================

    public function test_redeem_over_consumption_marks_whole_score_as_used(): void
    {
        $user  = User::factory()->create();
        $score = Score::factory()->create(['user_id' => $user->id, 'amount' => 100, 'status' => 1]);
        $prize = Prize::factory()->create(['cost' => 60, 'status' => 1]);

        $this->makeService()->redeem($user, $prize->id);

        // Entire 100pt score consumed — no 40pt remainder left as active
        $this->assertSame(0, $score->fresh()->status);
        // No remaining active scores
        $this->assertSame(0, Score::where('user_id', $user->id)->where('status', 1)->sum('amount'));
    }

    // =========================================================================
    // 5. Insufficient scores → HttpException 422
    // =========================================================================

    public function test_redeem_insufficient_scores_throws_422(): void
    {
        $user  = User::factory()->create();
        Score::factory()->create(['user_id' => $user->id, 'amount' => 50, 'status' => 1]);
        $prize = Prize::factory()->create(['cost' => 100, 'status' => 1]);

        try {
            $this->makeService()->redeem($user, $prize->id);
            $this->fail('Expected HttpException with status 422 was not thrown');
        } catch (HttpException $e) {
            $this->assertSame(422, $e->getStatusCode());
        }

        $this->assertSame(0, PrizeRedemption::count());
    }

    // =========================================================================
    // 6. Non-existent prize_id → HttpException 404
    // =========================================================================

    public function test_redeem_nonexistent_prize_throws_404(): void
    {
        $user   = User::factory()->create();
        Score::factory()->create(['user_id' => $user->id, 'amount' => 500, 'status' => 1]);
        $fakeId = Str::uuid()->toString();

        try {
            $this->makeService()->redeem($user, $fakeId);
            $this->fail('Expected HttpException with status 404 was not thrown');
        } catch (HttpException $e) {
            $this->assertSame(404, $e->getStatusCode());
        }
    }

    // =========================================================================
    // 7. Inactive prize (status=0) → HttpException 404
    // =========================================================================

    public function test_redeem_inactive_prize_throws_404(): void
    {
        $user  = User::factory()->create();
        Score::factory()->create(['user_id' => $user->id, 'amount' => 500, 'status' => 1]);
        $prize = Prize::factory()->inactive()->create(['cost' => 100]);

        try {
            $this->makeService()->redeem($user, $prize->id);
            $this->fail('Expected HttpException with status 404 was not thrown');
        } catch (HttpException $e) {
            $this->assertSame(404, $e->getStatusCode());
        }
    }

    // =========================================================================
    // 8. Already-spent scores (status=0) are skipped — not re-consumed
    // =========================================================================

    public function test_redeem_skips_already_spent_scores(): void
    {
        $user        = User::factory()->create();
        $spentScore  = Score::factory()->create(['user_id' => $user->id, 'amount' => 1000, 'status' => 0]);
        $activeScore = Score::factory()->create(['user_id' => $user->id, 'amount' => 200,  'status' => 1]);
        $prize       = Prize::factory()->create(['cost' => 100, 'status' => 1]);

        $this->makeService()->redeem($user, $prize->id);

        // Spent score was not touched
        $this->assertSame(0, $spentScore->fresh()->status);
        // Active score was consumed
        $this->assertSame(0, $activeScore->fresh()->status);
        $this->assertSame(1, PrizeRedemption::where('user_id', $user->id)->count());
    }

    // =========================================================================
    // 9. Redemption record fields — status, approved_at, code, snapshot
    // =========================================================================

    public function test_redeem_redemption_record_fields_are_correct(): void
    {
        $user  = User::factory()->create();
        Score::factory()->create(['user_id' => $user->id, 'amount' => 500, 'status' => 1]);
        $prize = Prize::factory()->create([
            'name'   => 'Gold Medal',
            'amount' => 5,
            'cost'   => 200,
            'status' => 1,
        ]);

        $redemption = $this->makeService()->redeem($user, $prize->id);
        $fresh      = $redemption->fresh();

        // Status and timestamps
        $this->assertSame('approved', $fresh->status);
        $this->assertNotNull($fresh->approved_at);

        // Redemption code format: PREFIX-XXXXXX
        $this->assertNotNull($fresh->redemption_code);
        $this->assertMatchesRegularExpression('/^[A-Z]+-[A-Z0-9]{6}$/', $fresh->redemption_code);

        // Snapshot fields — copied from prize at redemption time
        $this->assertSame($prize->id,   $fresh->prize_id);
        $this->assertSame('Gold Medal', $fresh->prize_name);
        $this->assertSame(5,            $fresh->prize_amount);
        $this->assertSame(200,          $fresh->score_cost);
        $this->assertSame($user->id,    $fresh->user_id);
    }

    // =========================================================================
    // 10. Transaction rollback — Score::save() throws → no redemption, no score changes
    // =========================================================================

    public function test_redeem_rollback_on_score_save_exception(): void
    {
        $user  = User::factory()->create();
        $score = Score::factory()->create(['user_id' => $user->id, 'amount' => 200, 'status' => 1]);
        $prize = Prize::factory()->create(['cost' => 100, 'status' => 1]);

        // Register a one-shot saving listener that throws on the first Score save inside the transaction
        $thrown = false;
        Score::saving(function () use (&$thrown) {
            if (!$thrown) {
                $thrown = true;
                throw new \RuntimeException('Simulated DB failure during score save');
            }
        });

        try {
            $this->makeService()->redeem($user, $prize->id);
            $this->fail('Expected RuntimeException was not thrown');
        } catch (\RuntimeException $e) {
            $this->assertSame('Simulated DB failure during score save', $e->getMessage());
        } finally {
            // Remove the listener to avoid polluting subsequent tests
            Score::flushEventListeners();
            Score::boot();
        }

        // Transaction rolled back — no redemption record persisted
        $this->assertSame(0, PrizeRedemption::count());

        // Score status unchanged — still active
        $this->assertSame(1, $score->fresh()->status);
    }
}

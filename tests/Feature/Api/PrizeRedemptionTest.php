<?php

namespace Tests\Feature\Api;

use App\Models\Prize;
use App\Models\PrizeRedemption;
use App\Models\Score;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class PrizeRedemptionTest extends TestCase
{
    use RefreshDatabase;

    // =========================================================================
    // GET /api/prizes — listPrizes
    // =========================================================================

    public function test_list_prizes_unauthenticated_returns_401(): void
    {
        $response = $this->getJson('/api/prizes');

        $response->assertStatus(401);
    }

    public function test_list_prizes_no_prizes_returns_empty_data_with_score_balance(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson('/api/prizes');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('user.total_scores', 0)
            ->assertJsonPath('user.available_scores', 0)
            ->assertJsonPath('user.spent_scores', 0)
            ->assertJsonCount(0, 'data.data');
    }

    public function test_list_prizes_returns_only_active_prizes(): void
    {
        $user = User::factory()->create();

        $active1  = Prize::factory()->create(['status' => 1]);
        $active2  = Prize::factory()->create(['status' => 1]);
        $inactive = Prize::factory()->inactive()->create();

        $response = $this->actingAs($user)->getJson('/api/prizes');

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data.data');

        $ids = collect($response->json('data.data'))->pluck('id');
        $this->assertContains($active1->id, $ids);
        $this->assertContains($active2->id, $ids);
        $this->assertNotContains($inactive->id, $ids);
    }

    public function test_list_prizes_can_afford_field_reflects_user_balance(): void
    {
        $user = User::factory()->create();

        Score::factory()->create([
            'user_id' => $user->id,
            'amount'  => 100,
            'status'  => 1,
        ]);

        $cheapPrize     = Prize::factory()->create(['cost' => 50,  'status' => 1]);
        $expensivePrize = Prize::factory()->create(['cost' => 200, 'status' => 1]);

        $response = $this->actingAs($user)->getJson('/api/prizes');

        $response->assertStatus(200)
            ->assertJsonPath('user.available_scores', 100);

        $items = collect($response->json('data.data'));

        $cheap     = $items->firstWhere('id', $cheapPrize->id);
        $expensive = $items->firstWhere('id', $expensivePrize->id);

        $this->assertTrue($cheap['can_afford']);
        $this->assertFalse($expensive['can_afford']);
    }

    public function test_list_prizes_score_balance_totals_are_correct(): void
    {
        $user = User::factory()->create();

        // 3 active scores totalling 300
        Score::factory()->count(3)->create([
            'user_id' => $user->id,
            'amount'  => 100,
            'status'  => 1,
        ]);

        // One past approved redemption costs 80 — reduces available balance
        $prize = Prize::factory()->create(['cost' => 80, 'status' => 1]);
        PrizeRedemption::factory()->create([
            'user_id'    => $user->id,
            'prize_id'   => $prize->id,
            'score_cost' => 80,
            'status'     => 'approved',
        ]);

        $response = $this->actingAs($user)->getJson('/api/prizes');

        $response->assertStatus(200)
            ->assertJsonPath('user.total_scores', 300)
            ->assertJsonPath('user.available_scores', 220)
            ->assertJsonPath('user.spent_scores', 80);
    }

    // =========================================================================
    // POST /api/prizes/redeem — redeem
    // =========================================================================

    public function test_redeem_unauthenticated_returns_401(): void
    {
        $response = $this->postJson('/api/prizes/redeem', ['prize_id' => Str::uuid()->toString()]);

        $response->assertStatus(401);
    }

    public function test_redeem_missing_prize_id_returns_422(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/prizes/redeem', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['prize_id']);
    }

    public function test_redeem_nonexistent_prize_id_returns_422(): void
    {
        $user = User::factory()->create();

        // exists:prizes,id validation fires before controller body — yields 422
        $response = $this->actingAs($user)->postJson('/api/prizes/redeem', [
            'prize_id' => Str::uuid()->toString(),
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['prize_id']);
    }

    public function test_redeem_inactive_prize_returns_404(): void
    {
        $user = User::factory()->create();

        Score::factory()->create([
            'user_id' => $user->id,
            'amount'  => 500,
            'status'  => 1,
        ]);

        // Prize exists in DB (passes exists:prizes,id), but status=0 — 404 inside transaction
        $prize = Prize::factory()->inactive()->create(['cost' => 100]);

        $response = $this->actingAs($user)->postJson('/api/prizes/redeem', [
            'prize_id' => $prize->id,
        ]);

        $response->assertStatus(404)
            ->assertJsonPath('success', false);
    }

    public function test_redeem_insufficient_scores_returns_422(): void
    {
        $user = User::factory()->create();

        Score::factory()->create([
            'user_id' => $user->id,
            'amount'  => 50,
            'status'  => 1,
        ]);

        $prize = Prize::factory()->create(['cost' => 100, 'status' => 1]);

        $response = $this->actingAs($user)->postJson('/api/prizes/redeem', [
            'prize_id' => $prize->id,
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('success', false);

        $this->assertStringContainsStringIgnoringCase('insufficient', $response->json('message'));
    }

    public function test_redeem_happy_path_creates_redemption_and_returns_201(): void
    {
        $user = User::factory()->create();

        Score::factory()->create([
            'user_id' => $user->id,
            'amount'  => 200,
            'status'  => 1,
        ]);

        $prize = Prize::factory()->create(['cost' => 100, 'status' => 1]);

        $response = $this->actingAs($user)->postJson('/api/prizes/redeem', [
            'prize_id' => $prize->id,
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('redemption.prize_id', $prize->id)
            ->assertJsonPath('redemption.score_cost', 100)
            ->assertJsonPath('redemption.status', 'approved')
            ->assertJsonPath('new_balance.available_scores', 100);

        $this->assertNotNull($response->json('redemption.redemption_code'));
        $this->assertNotNull($response->json('redemption.approved_at'));
        $this->assertMatchesRegularExpression(
            '/^[A-Z]+-[A-Z0-9]{6}$/',
            $response->json('redemption.redemption_code')
        );

        $this->assertDatabaseHas('prize_redemptions', [
            'user_id'    => $user->id,
            'prize_id'   => $prize->id,
            'score_cost' => 100,
            'status'     => 'approved',
        ]);
    }

    public function test_redeem_sequential_redemptions_reduce_balance_and_block_third(): void
    {
        $user = User::factory()->create();

        Score::factory()->create([
            'user_id' => $user->id,
            'amount'  => 200,
            'status'  => 1,
        ]);

        $prize = Prize::factory()->create(['cost' => 100, 'status' => 1]);

        // First redemption — succeeds, balance drops to 100
        $this->actingAs($user)->postJson('/api/prizes/redeem', ['prize_id' => $prize->id])
            ->assertStatus(201)
            ->assertJsonPath('new_balance.available_scores', 100);

        // Second redemption — succeeds, balance drops to 0
        $this->actingAs($user)->postJson('/api/prizes/redeem', ['prize_id' => $prize->id])
            ->assertStatus(201)
            ->assertJsonPath('new_balance.available_scores', 0);

        // Third redemption — fails (insufficient scores)
        $this->actingAs($user)->postJson('/api/prizes/redeem', ['prize_id' => $prize->id])
            ->assertStatus(422);

        $this->assertSame(2, PrizeRedemption::where('user_id', $user->id)->count());
    }

    // =========================================================================
    // GET /api/prizes/redemptions — redemptions
    // =========================================================================

    public function test_redemptions_unauthenticated_returns_401(): void
    {
        $response = $this->getJson('/api/prizes/redemptions');

        $response->assertStatus(401);
    }

    public function test_redemptions_returns_only_own_redemptions(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        PrizeRedemption::factory()->count(2)->create(['user_id' => $userA->id]);
        PrizeRedemption::factory()->create(['user_id' => $userB->id]);

        $response = $this->actingAs($userA)->getJson('/api/prizes/redemptions');

        $response->assertStatus(200)
            ->assertJsonPath('meta.total', 2);

        $returnedIds     = collect($response->json('data'))->pluck('id');
        $userBRedemptions = PrizeRedemption::where('user_id', $userB->id)->pluck('id');

        foreach ($userBRedemptions as $id) {
            $this->assertNotContains($id, $returnedIds);
        }
    }

    public function test_redemptions_correct_pagination_structure(): void
    {
        $user = User::factory()->create();

        PrizeRedemption::factory()->count(3)->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->getJson('/api/prizes/redemptions');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data',
                'meta' => ['current_page', 'last_page', 'per_page', 'total'],
            ])
            ->assertJsonPath('meta.per_page', 15)
            ->assertJsonPath('meta.total', 3)
            ->assertJsonPath('meta.current_page', 1);
    }

    public function test_redemptions_empty_history_returns_empty_data(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson('/api/prizes/redemptions');

        $response->assertStatus(200)
            ->assertJsonPath('meta.total', 0)
            ->assertJsonCount(0, 'data');
    }

    // =========================================================================
    // GET /api/prizes/redemptions/{id} — showRedemption
    // =========================================================================

    public function test_show_redemption_unauthenticated_returns_401(): void
    {
        $response = $this->getJson('/api/prizes/redemptions/' . Str::uuid()->toString());

        $response->assertStatus(401);
    }

    public function test_show_redemption_not_found_returns_404(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->getJson('/api/prizes/redemptions/' . Str::uuid()->toString());

        $response->assertStatus(404)
            ->assertJsonPath('success', false);
    }

    public function test_show_redemption_other_users_redemption_returns_403(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $redemption = PrizeRedemption::factory()->create(['user_id' => $userB->id]);

        $response = $this->actingAs($userA)
            ->getJson('/api/prizes/redemptions/' . $redemption->id);

        $response->assertStatus(403)
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'Forbidden');
    }

    public function test_show_redemption_own_returns_200_with_correct_structure(): void
    {
        $user = User::factory()->create();

        $redemption = PrizeRedemption::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)
            ->getJson('/api/prizes/redemptions/' . $redemption->id);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.id', $redemption->id)
            ->assertJsonPath('data.status', 'approved')
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'prize_id',
                    'prize_name',
                    'prize_amount',
                    'score_cost',
                    'status',
                    'redemption_code',
                    'notes',
                    'redeemed_at',
                    'approved_at',
                    'completed_at',
                    'cancelled_at',
                ],
            ]);

        $this->assertNotNull($response->json('data.prize_name'));
        $this->assertNotNull($response->json('data.redemption_code'));
        $this->assertIsInt($response->json('data.score_cost'));
    }
}

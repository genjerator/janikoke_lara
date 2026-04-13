<?php

namespace App\Services;

use App\Models\Prize;
use App\Models\PrizeRedemption;
use App\Models\Score;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PrizeRedemptionService
{
    public function __construct(private readonly ScoreService $scoreService)
    {
    }

    public function redeem(User $user, string $prizeId): PrizeRedemption
    {
        return DB::transaction(function () use ($user, $prizeId) {
            // Re-check available scores inside transaction to prevent race conditions
            $availableScores = $this->scoreService->getAvailableScores($user);

            $prize = Prize::where('id', $prizeId)
                ->where('status', 1)
                ->lockForUpdate()
                ->first();

            if (!$prize) {
                abort(404, 'Prize not found or not available');
            }

            if ($availableScores < $prize->cost) {
                abort(422, 'Insufficient scores to redeem this prize');
            }

            $redemption = PrizeRedemption::create([
                'user_id'         => $user->id,
                'prize_id'        => $prize->id,
                'prize_name'      => $prize->name,
                'prize_amount'    => $prize->amount,
                'score_cost'      => $prize->cost,
                'status'          => 'approved',
                'redemption_code' => PrizeRedemption::generateCode($prize->name),
                'redeemed_at'     => now(),
                'approved_at'     => now(),
            ]);

            $this->consumeScoresFifo($user->id, $prize->cost);

            return $redemption;
        });
    }

    /**
     * Mark the oldest active scores as status=0 (used) up to the required amount.
     * Consumes whole score records FIFO (oldest first).
     */
    private function consumeScoresFifo(int $userId, int $required): void
    {
        $remaining = $required;

        Score::where('user_id', $userId)
            ->where('status', 1)
            ->orderBy('created_at')
            ->orderBy('id')
            ->each(function (Score $score) use (&$remaining) {
                if ($remaining <= 0) {
                    return false; // stop iteration
                }

                $score->status = 0;
                $score->save();

                $remaining -= $score->amount;
            });
    }
}

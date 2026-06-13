<?php

namespace App\Services;

use App\Models\Prize;
use App\Models\PrizeRedemption;
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

            // Decrement prize stock by 1
            $prize->decrement('amount');

            // The redemption row (score_cost) is the source of truth for spend.
            // Available balance is derived as earned - sum(score_cost), so we no
            // longer flip score rows (which over-spent on non-multiple costs).

            return $redemption;
        });
    }
}

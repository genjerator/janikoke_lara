<?php

namespace Database\Factories;

use App\Models\Prize;
use App\Models\PrizeRedemption;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PrizeRedemption>
 */
class PrizeRedemptionFactory extends Factory
{
    protected $model = PrizeRedemption::class;

    public function definition(): array
    {
        $prize = Prize::factory()->create();

        return [
            'user_id'         => User::factory(),
            'prize_id'        => $prize->id,
            'prize_name'      => $prize->name,
            'prize_amount'    => $prize->amount,
            'score_cost'      => $prize->cost,
            'status'          => 'approved',
            'redemption_code' => PrizeRedemption::generateCode($prize->name),
            'notes'           => null,
            'redeemed_at'     => now(),
            'approved_at'     => now(),
            'completed_at'    => null,
            'cancelled_at'    => null,
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\Score;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Score>
 */
class ScoreFactory extends Factory
{
    protected $model = Score::class;

    public function definition(): array
    {
        return [
            'user_id'           => User::factory(),
            'challenge_area_id' => null,
            'round_id'          => null,
            'amount'            => fake()->numberBetween(50, 1000),
            'status'            => 1,
            'name'              => fake()->words(2, true),
            'description'       => null,
        ];
    }
}

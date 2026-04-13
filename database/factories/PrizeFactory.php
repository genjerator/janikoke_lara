<?php

namespace Database\Factories;

use App\Models\Prize;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Prize>
 */
class PrizeFactory extends Factory
{
    protected $model = Prize::class;

    public function definition(): array
    {
        return [
            'name'        => fake()->words(3, true),
            'description' => fake()->sentence(),
            'content'     => fake()->paragraph(),
            'amount'      => fake()->numberBetween(1, 100),
            'cost'        => fake()->numberBetween(10, 500),
            'status'      => 1,
            'image'       => null,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 0,
        ]);
    }
}

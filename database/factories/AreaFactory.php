<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use MatanYadaev\EloquentSpatial\Objects\LineString;
use MatanYadaev\EloquentSpatial\Objects\Point;
use MatanYadaev\EloquentSpatial\Objects\Polygon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Area>
 */
class AreaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'point' => new Point(51.5032973, -0.1217424),
            'area' => new Polygon([
                new LineString([
                    new Point(12.455363273620605, 41.90746728266806),
                    new Point(12.450309991836548, 41.906636872349075),
                    new Point(12.445632219314575, 41.90197359839437),
                    new Point(12.447413206100464, 41.90027269624499),
                    new Point(12.457906007766724, 41.90000118654431),
                    new Point(12.458517551422117, 41.90281205461268),
                    new Point(12.457584142684937, 41.903107507989986),
                    new Point(12.457734346389769, 41.905918239316286),
                    new Point(12.45572805404663, 41.90637337450963),
                    new Point(12.455363273620605, 41.90746728266806),
                ]),
            ]),
        ];
    }
}

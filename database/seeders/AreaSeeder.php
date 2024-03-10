<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\Challenge;
use App\Models\ChallengeArea;
use App\Models\Round;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use MatanYadaev\EloquentSpatial\Objects\LineString;
use MatanYadaev\EloquentSpatial\Objects\Point;
use MatanYadaev\EloquentSpatial\Objects\Polygon;

class AreaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if(!Area::where('name', 'Liman 3 building')->exists()) {
            $areaBuilding = Area::factory()->create([
                'name' => 'Liman 3 building',
                'description' => 'Liman 3 building',
                'point' => null,
                'area' => new Polygon([
                    new LineString([
                        new Point(19.8382080, 45.2382009),
                        new Point(19.8382777, 45.2380537),
                        new Point(19.8386908, 45.2381405),
                        new Point(19.8385674, 45.2383369),
                        new Point(19.8382080, 45.2382009),
                    ]),
                ]),
            ]);
            $challenge = Challenge::where('name', 'Liman 3')->first();

                ChallengeArea::factory()->create([
                    'challenge_id' => $challenge->id,
                    'area_id' => $areaBuilding->id
                ]);
        }
    }
}

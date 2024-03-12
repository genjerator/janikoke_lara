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

class ChallengeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $round = Round::where('name', 'Round1')->first();

        if(!Area::where('name', 'Quay mornarica')->exists()) {
            $areaMornarica = Area::factory()->create([
                'name' => 'Quay mornarica',
                'description' => 'Quay mornarica',
                'point' => null,
                'area' => new Polygon([
                    new LineString([
                        new Point(19.8359388, 45.2343860),
                        new Point(19.8360300, 45.2341519),
                        new Point(19.8373067, 45.2343785),
                        new Point(19.8371726, 45.2346164),
                        new Point(19.8359388, 45.2343860),
                    ]),
                ]),
            ]);
        }

        if(!Area::where('name', 'Quay strand')->exists()) {
            $areaStrand = Area::factory()->create([
                'name' => 'Quay strand',
                'description' => 'Quay strand',
                'point' => null,
                'area' => new Polygon([
                    new LineString([
                        new Point(19.8553419, 45.2509579),
                        new Point(19.8553419, 45.2494023),
                        new Point(19.8563290, 45.2493872),
                        new Point(19.8563504, 45.2509428),
                        new Point(19.8553419, 45.2509579),
                    ]),
                ]),
            ]);
        }

        if(!Area::where('name', 'Quay zepelin')->exists()) {
            $areaZepelin = Area::factory()->create([
                'name' => 'Quay zepelin',
                'description' => 'Quay zepelin',
                'point' => null,
                'area' => new Polygon([
                    new LineString([
                        new Point(19.8359388, 45.2343860),
                        new Point(19.8360300, 45.2341519),
                        new Point(19.8373067, 45.2343785),
                        new Point(19.8371726, 45.2346164),
                        new Point(19.8359388, 45.2343860),
                    ]),
                ]),
            ]);
        }
       // if(!Challenge::where('name', 'Quay')->exists()) {
//            $challenge = Challenge::factory()->create([
//                'round_id' => $round->id,
//                'name' => 'Quay',
//                'description' => 'Quay',
//            ]);

            ChallengeArea::factory()->create([
                'challenge_id' => 3,
                'area_id' => 8
            ]);
            ChallengeArea::factory()->create([
                'challenge_id' => 3,
                'area_id' => 9
            ]);ChallengeArea::factory()->create([
                'challenge_id' => 3,
                'area_id' => 10
            ]);
       // }
    }
}

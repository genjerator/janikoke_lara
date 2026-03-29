<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Area;
use App\Models\Challenge;
use App\Models\ChallengeArea;
use App\Models\Round;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use MatanYadaev\EloquentSpatial\Objects\LineString;
use MatanYadaev\EloquentSpatial\Objects\Point;
use MatanYadaev\EloquentSpatial\Objects\Polygon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (! Round::where('name', 'Round1')->exists()) {
            $round = Round::factory()->create([
                'name' => 'Round1',
                'description' => 'Round1',
                'starts_at' => Carbon::now()->startOfYear(),
                'ends_at' => Carbon::now()->endOfYear(),
            ]);
        }
        if (! Area::where('name', 'Liman 3 parking')->exists()) {
            $areaParking = Area::factory()->create([
                'name' => 'Liman 3 parking',
                'description' => 'Liman 3 parking',
                'point' => null,
                'area' => new Polygon([
                    new LineString([
                        new Point(45.2376218, 19.8380899),
                        new Point(45.2372442, 19.8381758),
                        new Point(45.2373046, 19.8388731),
                        new Point(45.2380372, 19.8388731),
                        new Point(45.2376218, 19.8380899),
                    ]),
                ]),
            ]);
        }
        if (! Area::where('name', 'Liman 3 park')->exists()) {
            $areaPark = Area::factory()->create([
                'name' => 'Liman 3 park',
                'description' => 'Liman 3 park',
                'point' => null,
                'area' => new Polygon([
                    new LineString([
                        new Point(45.2395553, 19.8383689),
                        new Point(45.2376520, 19.8394525),
                        new Point(45.2382940, 19.8425746),
                        new Point(45.2402804, 19.8414052),
                        new Point(45.2395553, 19.8383689),
                    ]),
                ]),
            ]);
        }
        if (! Challenge::where('name', 'Liman 3')->exists()) {
            $challenge = Challenge::factory()->create([
                'round_id' => $round->id,
                'name' => 'Liman 3',
                'description' => 'Liman 3',
            ]);

            ChallengeArea::factory()->create([
                'challenge_id' => $challenge->id,
                'area_id' => $areaParking->id,
            ]);
            ChallengeArea::factory()->create([
                'challenge_id' => $challenge->id,
                'area_id' => $areaPark->id,
            ]);
        }
        $this->call([
            AreaSeeder::class,
            AreaArticleSeeder::class, // Creates 3 articles per area
            PrizeSeeder::class, // Creates 5 beer prizes with amount 20
            // Uncomment to seed current production data:
            // CurrentAreasSeeder::class,
            // CurrentChallengesSeeder::class,
        ]);
    }
}

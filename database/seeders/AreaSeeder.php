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
        $challenge = Challenge::where('name', 'Liman 3')->first();
        if (!Area::where('name', 'Liman 3 building')->exists()) {
            $areaBuilding = Area::factory()->create([
                'name' => 'Liman 3 building',
                'description' => 'Liman 3 building',
                'point' => null,
                'area' => new Polygon([
                    new LineString([
//                        new Point(19.8382080, 45.2382009),
//                        new Point(19.8382777, 45.2380537),
//                        new Point(19.8386908, 45.2381405),
//                        new Point(19.8385674, 45.2383369),
//                        new Point(19.8382080, 45.2382009),
                        new Point(45.2382009, 19.8382080),
                        new Point(45.2380537, 19.8382777),
                        new Point(45.2381405, 19.8386908),
                        new Point(45.2383369, 19.8385674),
                        new Point(45.2382009, 19.8382080),
                    ]),
                ]),
            ]);

            ChallengeArea::factory()->create([
                'challenge_id' => $challenge->id,
                'area_id' => $areaBuilding->id
            ]);
        }
        $this->limanskiPark();
        $this->betjar();
        $this->rk();
        //$this->test();
    }

    public function limanskiPark(): void
    {
        $challenge = Challenge::where('name', 'Limanski park')->first();

        if(!$challenge) {
            $challenge = Challenge::factory()->create([
                'round_id' => 1,
                'name' => 'Limanski park',
                'description' => 'Limanski park',
            ]);
        }


        if (!Area::where('name', 'Skate park')->exists()) {
            $areaBuilding = Area::factory()->create([
                'name' => 'Skate park',
                'description' => 'Liman 3 skate park',
                'point' => null,
                'area' => new Polygon([
                    new LineString([
//                        new Point(19.8419362, 45.2385382),
//                        new Point(19.8421401, 45.2382058),
//                        new Point(19.8426443, 45.2383116),
//                        new Point(19.8424512, 45.2387044),
//                        new Point(19.8419362, 45.2385382),
                        new Point(45.2385382, 19.8419362),
                        new Point(45.2382058, 19.8421401),
                        new Point(45.2383116, 19.8426443),
                        new Point(45.2387044, 19.8424512),
                        new Point(45.2385382, 19.8419362),
                    ]),
                ]),
            ]);

            ChallengeArea::factory()->create([
                'challenge_id' => $challenge->id,
                'area_id' => $areaBuilding->id
            ]);
        }

        if (!Area::where('name', 'Covek jelen')->exists()) {

            $areaBuilding = Area::factory()->create([
                'name' => 'Covek jelen',
                'description' => 'Liman 3 covek jelen',
                'point' => null,
                'area' => new Polygon([
                    new LineString([
//                        new Point(19.8408844, 45.2394033),
//                        new Point(19.8409411, 45.2392137),
//                        new Point(19.8411933, 45.2392515),
//                        new Point(19.8411503, 45.2394403),
//                        new Point(19.8408844, 45.2394033),
                        new Point(45.2394033, 19.8408844),
                        new Point(45.2392137, 19.8409411),
                        new Point(45.2392515, 19.8411933),
                        new Point(45.2394403, 19.8411503),
                        new Point(45.2394033, 19.8408844),
                    ]),
                ]),
            ]);

            ChallengeArea::factory()->create([
                'challenge_id' => $challenge->id,
                'area_id' => $areaBuilding->id
            ]);
        }

        if (!Area::where('name', 'Jarboli')->exists()) {
            $areaBuilding = Area::factory()->create([
                'name' => 'Jarboli',
                'description' => 'Liman 3 Jarboli',
                'point' => null,
                'area' => new Polygon([
                    new LineString([
//                        new Point(19.8420253, 45.2412479),
//                        new Point(19.8422024, 45.2410024),
//                        new Point(19.8426261, 45.2410893),
//                        new Point(19.8424759, 45.2413726),
//                        new Point(19.8420253, 45.2412479),
                        new Point(45.2412479, 19.8420253),
                        new Point(45.2410024, 19.8422024),
                        new Point(45.2410893, 19.8426261),
                        new Point(45.2413726, 19.8424759),
                        new Point(45.2412479, 19.8420253),
                    ]),
                ]),
            ]);
            ChallengeArea::factory()->create([
                'challenge_id' => $challenge->id,
                'area_id' => $areaBuilding->id
            ]);
        }
        if (!Area::where('name', 'Dvoriste')->exists()) {
            $areaBuilding = Area::factory()->create([
                'name' => 'Dvoriste',
                'description' => 'Liman 3 dvoriste',
                'point' => null,
                'area' => new Polygon([
                    new LineString([
//                        new Point(19.8375867,45.2375652 ),
//                        new Point(19.8378012,45.2370741 ),
//                        new Point(19.8382733,45.2372479),
//                        new Point(19.8380909,45.2376634 ),
//                        new Point(19.8375867,45.2375652 ),
                        new Point(45.2375652, 19.8375867),
                        new Point(45.2370741, 19.8378012),
                        new Point(45.2372479, 19.8382733),
                        new Point(45.2376634, 19.8380909),
                        new Point(45.2375652, 19.8375867),
                    ]),
                ]),
            ]);
            ChallengeArea::factory()->create([
                'challenge_id' => $challenge->id,
                'area_id' => $areaBuilding->id
            ]);
        }
    }

    public function betjar(): void
    {
        $challenge = Challenge::where('name', 'Becarusa')->first();

        if(!$challenge) {
            $challenge = Challenge::factory()->create([
                'round_id' => 1,
                'name' => 'Becarusa',
                'description' => 'Becarusa',
            ]);
        }

        if (!Area::where('name', 'Kod Jove')->exists()) {
            $areaBuilding = Area::factory()->create([
                'name' => 'Kod Jove',
                'description' => 'Kod Jove',
                'point' => null,
                'area' => new Polygon([
                    new LineString([
//                        new Point(19.8419521,45.23554 ),
//                        new Point(19.842172,45.2351849 ),
//                        new Point(19.8431752,45.2350565 ),
//                        new Point(19.8433522,45.2353285 ),
//                        new Point(19.8419521, 45.23554 ),
                        new Point(45.23554, 19.8419521),
                        new Point(45.2351849, 19.842172),
                        new Point(45.2350565, 19.8431752),
                        new Point(45.2353285, 19.8433522),
                        new Point(45.23554, 19.8419521),
                    ]),
                ]),
            ]);
            ChallengeArea::factory()->create([
                'challenge_id' => $challenge->id,
                'area_id' => $areaBuilding->id
            ]);
        }
        if (!Area::where('name', 'Matka')->exists()) {
            $areaBuilding = Area::factory()->create([
                'name' => 'Matka',
                'description' => 'Matka',
                'point' => null,
                'area' => new Polygon([
                    new LineString([
//                        new Point(19.8432148,45.2610558 ),
//                        new Point(19.8431558,45.2607084 ),
//                        new Point(19.8435796,45.260731 ),
//                        new Point(19.8437351,45.2609387 ),
//                        new Point(19.8432148,45.2610558 ),
                        new Point(45.2610558, 19.8432148),
                        new Point(45.2607084, 19.8431558),
                        new Point(45.260731, 19.8435796),
                        new Point(45.2609387, 19.8437351),
                        new Point(45.2610558, 19.8432148),
                    ]),
                ]),
            ]);
            ChallengeArea::factory()->create([
                'challenge_id' => $challenge->id,
                'area_id' => $areaBuilding->id
            ]);
        }


        if (!Area::where('name', 'Pivnica')->exists()) {
            $areaBuilding = Area::factory()->create([
                'name' => 'Pivnica',
                'description' => 'Pivnica',
                'point' => null,
                'area' => new Polygon([
                    new LineString([
//                        new Point(19.8431458, 45.2564705),
//                        new Point(19.8446317, 45.2552885),
//                        new Point(19.8453023, 45.2556737),
//                        new Point(19.8439451, 45.2567122),
//                        new Point(19.8431458, 45.2564705),
                        new Point(45.2564705, 19.8431458),
                        new Point(45.2552885, 19.8446317),
                        new Point(45.2556737, 19.8453023),
                        new Point(45.2567122, 19.8439451),
                        new Point(45.2564705, 19.8431458),
                    ]),
                ]),
            ]);
            ChallengeArea::factory()->create([
                'challenge_id' => $challenge->id,
                'area_id' => $areaBuilding->id
            ]);
        }

        if (!Area::where('name', 'Betjarac')->exists()) {
            $areaBuilding = Area::factory()->create([
                'name' => 'Betjarac',
                'description' => 'Betjarac',
                'point' => null,
                'area' => new Polygon([
                    new LineString([
//                        new Point(19.854081, 45.2487686),
//                        new Point(19.8554758, 45.2452637),
//                        new Point(19.8569778, 45.2460644),
//                        new Point(19.8562053, 45.249539),
//                        new Point(19.854081, 45.2487686),
                        new Point(45.2487686, 19.854081),
                        new Point(45.2452637, 19.8554758),
                        new Point(45.2460644, 19.8569778),
                        new Point(45.249539, 19.8562053),
                        new Point(45.2487686, 19.854081),
                    ]),
                ]),
            ]);
            ChallengeArea::factory()->create([
                'challenge_id' => $challenge->id,
                'area_id' => $areaBuilding->id
            ]);
        }
    }

    public function rk(): void
    {
        $challenge = Challenge::where('name', 'Ruski Kerestur')->first();

        if(!$challenge) {
            $challenge = Challenge::factory()->create([
                'round_id' => 1,
                'name' => 'Ruski Kerestur',
                'description' => 'Ruski Kerestur',
            ]);
        }

        if (!Area::where('name', 'Trokut')->exists()) {
            $areaBuilding = Area::factory()->create([
                'name' => 'Trokut',
                'description' => 'Trokut',
                'point' => null,
                'area' => new Polygon([
                    new LineString([
//                        new Point(19.4316891, 45.5424095),
//                        new Point(19.4306377, 45.5414852),
//                        new Point(19.4307235, 45.5397869),
//                        new Point(19.4337247, 45.5396116),
//                        new Point(19.4365814, 45.5394563),
//                        new Point(19.4365225, 45.5401928),
//                        new Point(19.4345447, 45.5411588),
//                        new Point(19.4316891, 45.5424095),
                        new Point(45.5424095, 19.4316891),
                        new Point(45.5414852, 19.4306377),
                        new Point(45.5397869, 19.4307235),
                        new Point(45.5396116, 19.4337247),
                        new Point(45.5394563, 19.4365814),
                        new Point(45.5401928, 19.4365225),
                        new Point(45.5411588, 19.4345447),
                        new Point(45.5424095, 19.4316891),

                    ]),
                ]),
            ]);
            ChallengeArea::factory()->create([
                'challenge_id' => $challenge->id,
                'area_id' => $areaBuilding->id
            ]);
        }
        if (!Area::where('name', 'Bazen L')->exists()) {
            $areaBuilding = Area::factory()->create([
                'name' => 'Bazen L',
                'description' => 'Bazen L',
                'point' => null,
                'area' => new Polygon([
                    new LineString([
//                        new Point(19.4007136, 45.5605097),
//                        new Point(19.4011856, 45.5600215),
//                        new Point(19.4014646, 45.5601041),
//                        new Point(19.4009818, 45.5606149),
//                        new Point(19.4007136, 45.5605097),
                        new Point(45.5605097, 19.4007136),
                        new Point(45.5600215, 19.4011856),
                        new Point(45.5601041, 19.4014646),
                        new Point(45.5606149, 19.4009818),
                        new Point(45.5605097, 19.4007136),
                    ]),
                ]),
            ]);
            ChallengeArea::factory()->create([
                'challenge_id' => $challenge->id,
                'area_id' => $areaBuilding->id
            ]);
        }


        if (!Area::where('name', 'Bazen R')->exists()) {
            $areaBuilding = Area::factory()->create([
                'name' => 'Bazen R',
                'description' => 'Bazen R',
                'point' => null,
                'area' => new Polygon([
                    new LineString([
//                        new Point(19.4004017, 45.5606161),
//                        new Point(19.3996990, 45.5606649),
//                        new Point(19.4006056, 45.5595231),
//                        new Point(19.4012707, 45.5598236),
//                        new Point(19.4004017, 45.5606161),
                        new Point(45.5606161, 19.4004017),
                        new Point(45.5606649, 19.3996990),
                        new Point(45.5595231, 19.4006056),
                        new Point(45.5598236, 19.4012707),
                        new Point(45.5606161, 19.4004017),
                    ]),
                ]),
            ]);
            ChallengeArea::factory()->create([
                'challenge_id' => $challenge->id,
                'area_id' => $areaBuilding->id
            ]);
        }

        if (!Area::where('name', 'Bombaj')->exists()) {
            $areaBuilding = Area::factory()->create([
                'name' => 'Bombaj',
                'description' => 'Bombaj',
                'point' => null,
                'area' => new Polygon([
                    new LineString([
//                        new Point(19.4130577, 45.5621498),
//                        new Point(19.4136102, 45.5616841),
//                        new Point(19.4141360, 45.5620258),
//                        new Point(19.4135191, 45.5623564),
//                        new Point(19.4130577, 45.5621498),
                        new Point(45.5621498, 19.4130577),
                        new Point(45.5616841, 19.4136102),
                        new Point(45.5620258, 19.4141360),
                        new Point(45.5623564, 19.4135191),
                        new Point(45.5621498, 19.4130577),
                    ]),
                ]),
            ]);
            ChallengeArea::factory()->create([
                'challenge_id' => $challenge->id,
                'area_id' => $areaBuilding->id
            ]);
        }

        if (!Area::where('name', 'Ljesik')->exists()) {
            $areaBuilding = Area::factory()->create([
                'name' => 'Ljesik',
                'description' => 'Ljesik',
                'point' => null,
                'area' => new Polygon([
                    new LineString([
//                        new Point(19.4084207, 45.5550776),
//                        new Point(19.4103841, 45.5540258),
//                        new Point(19.4129268, 45.5552128),
//                        new Point(19.4107060, 45.5562495),
//                        new Point(19.4084207, 45.5550776),
                        new Point(45.5550776, 19.4084207),
                        new Point(45.5540258, 19.4103841),
                        new Point(45.5552128, 19.4129268),
                        new Point(45.5562495, 19.4107060),
                        new Point(45.5550776, 19.4084207),
                    ]),
                ]),
            ]);
            ChallengeArea::factory()->create([
                'challenge_id' => $challenge->id,
                'area_id' => $areaBuilding->id
            ]);
        }
    }

    public function test(){
        $challenge = Challenge::where('id', 1)->first();
        if (!Area::where('name', 'test111')->exists()) {
            $areaBuilding = Area::factory()->create([
                'name' => 'test111',
                'description' => 'test111',
                'point' => null,
                'area' => new Polygon([
                    new LineString([
//                        new Point(19.8378394, 45.2383471),
//                        new Point(19.8380969, 45.2377503),
//                        new Point(19.8387729, 45.2377125),
//                        new Point(19.8386978, 45.2384302),
//                        new Point(19.8378394, 45.2383471)
                        new Point(45.2383471, 19.8378394),
                        new Point(45.2377503, 19.8380969),
                        new Point(45.2377125, 19.8387729),
                        new Point(45.2384302, 19.8386978),
                        new Point(45.2383471, 19.8378394),
                    ]),
                ]),
            ]);
            ChallengeArea::factory()->create([
                'challenge_id' => $challenge->id,
                'area_id' => $areaBuilding->id
            ]);
        }
    }
}

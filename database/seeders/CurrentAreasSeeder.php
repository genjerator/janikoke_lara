<?php

namespace Database\Seeders;

use App\Models\Area;
use Illuminate\Database\Seeder;
use MatanYadaev\EloquentSpatial\Objects\LineString;
use MatanYadaev\EloquentSpatial\Objects\Point;
use MatanYadaev\EloquentSpatial\Objects\Polygon;

class CurrentAreasSeeder extends Seeder
{
    /**
     * Seed the current areas from production database.
     */
    public function run(): void
    {
        $areas = $this->getAreasData();

        foreach ($areas as $areaData) {
            // Skip if area already exists
            if (Area::where('name', $areaData['name'])->exists()) {
                $this->command->info("Area '{$areaData['name']}' already exists, skipping...");
                continue;
            }

            // Create polygon from coordinates
            $points = [];
            foreach ($areaData['area_coordinates'] as $coords) {
                $points[] = new Point($coords[0], $coords[1]);
            }

            $lineString = new LineString($points);
            $polygon = new Polygon([$lineString]);

            // Create point if exists
            $point = $areaData['point'] ? new Point($areaData['point'][0], $areaData['point'][1]) : null;

            Area::create([
                'name' => $areaData['name'],
                'description' => $areaData['description'],
                'point' => $point,
                'area' => $polygon,
                'is_active' => $areaData['is_active'],
                'type' => $areaData['type'],
            ]);

            $this->command->info("Created area: {$areaData['name']}");
        }

        $this->command->info('Current areas seeded successfully!');
    }

    private function getAreasData(): array
    {
        return [
            [
                'name' => 'Kotorska Sever',
                'description' => 'kotorska Sever',
                'point' => [19.80944039221141, 45.235049946473254],
                'area_coordinates' => [
                    [19.8091526, 45.2349436],
                    [19.8093939, 45.2349247],
                    [19.8096193, 45.2349096],
                    [19.8097587, 45.23514],
                    [19.8092384, 45.2351967],
                    [19.8091526, 45.2349436],
                ],
                'is_active' => true,
                'type' => null,
            ],
            [
                'name' => 'Pod mostom',
                'description' => 'Pod mostom',
                'point' => [19.407865202203062, 45.55641678224054],
                'area_coordinates' => [
                    [19.4059317, 45.5565801],
                    [19.4083242, 45.5554682],
                    [19.4097511, 45.5563847],
                    [19.4072879, 45.5573746],
                    [19.4059317, 45.5565801],
                ],
                'is_active' => true,
                'type' => null,
            ],
            [
                'name' => 'Kerestur ulice',
                'description' => 'Kerestur ulice',
                'point' => [19.51316328124994, 45.545387732608315],
                'area_coordinates' => [
                    [19.5099685, 45.5471029],
                    [19.5156879, 45.5451923],
                    [19.5168824, 45.5435751],
                    [19.5105612, 45.5438224],
                    [19.5099685, 45.5471029],
                ],
                'is_active' => true,
                'type' => null,
            ],
            [
                'name' => 'Grbavica iza autoputa',
                'description' => 'Grbavica iza autoputa',
                'point' => [19.821732838628994, 45.26180695082862],
                'area_coordinates' => [
                    [19.8213866, 45.2606652],
                    [19.8223146, 45.2608027],
                    [19.8218447, 45.2625895],
                    [19.821132, 45.263017],
                    [19.8213866, 45.2606652],
                ],
                'is_active' => true,
                'type' => null,
            ],
            [
                'name' => 'Liman parkiralista',
                'description' => 'Liman parkiralista',
                'point' => [19.837681991606423, 45.238653598833886],
                'area_coordinates' => [
                    [19.8363441, 45.2380916],
                    [19.8388845, 45.2391322],
                    [19.8382933, 45.239336],
                    [19.8358886, 45.2383045],
                    [19.8363441, 45.2380916],
                ],
                'is_active' => true,
                'type' => null,
            ],
            [
                'name' => 'Telep parkiralista',
                'description' => 'Telep parkiralista',
                'point' => [19.82398539544748, 45.23986486098857],
                'area_coordinates' => [
                    [19.8222256, 45.2394051],
                    [19.8230956, 45.2392992],
                    [19.8258203, 45.2403152],
                    [19.8245484, 45.2405264],
                    [19.8222256, 45.2394051],
                ],
                'is_active' => true,
                'type' => null,
            ],
            [
                'name' => 'Liman 2 Parking',
                'description' => 'Liman 2 Parking',
                'point' => [19.826850924706904, 45.24127298742172],
                'area_coordinates' => [
                    [19.8263091, 45.2409124],
                    [19.8274661, 45.241448],
                    [19.8271924, 45.2416138],
                    [19.8260354, 45.2410972],
                    [19.8263091, 45.2409124],
                ],
                'is_active' => true,
                'type' => null,
            ],
            [
                'name' => 'Limanski Park Novo',
                'description' => 'Limanski Park Novo',
                'point' => [19.84059152821348, 45.23817662302736],
                'area_coordinates' => [
                    [19.8388417, 45.2392451],
                    [19.8416628, 45.2373066],
                    [19.8425937, 45.2380069],
                    [19.8407758, 45.2388947],
                    [19.8388417, 45.2392451],
                ],
                'is_active' => true,
                'type' => null,
            ],
            [
                'name' => 'Bulevarski park',
                'description' => 'Bulevarski park',
                'point' => [19.829892906895346, 45.24656043862832],
                'area_coordinates' => [
                    [19.8276848, 45.2454992],
                    [19.8318773, 45.2474693],
                    [19.8312223, 45.2476837],
                    [19.8269653, 45.2457622],
                    [19.8276848, 45.2454992],
                ],
                'is_active' => true,
                'type' => null,
            ],
            [
                'name' => 'Novi Sad Centar',
                'description' => 'Novi Sad Centar',
                'point' => [19.846084449913914, 45.25523978906319],
                'area_coordinates' => [
                    [19.8404986, 45.2547055],
                    [19.8517346, 45.2557695],
                    [19.8510687, 45.2560858],
                    [19.8399401, 45.2549696],
                    [19.8404986, 45.2547055],
                ],
                'is_active' => true,
                'type' => null,
            ],
        ];
    }
}

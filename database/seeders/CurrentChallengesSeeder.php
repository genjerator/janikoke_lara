<?php

namespace Database\Seeders;

use App\Models\Challenge;
use Illuminate\Database\Seeder;
use MatanYadaev\EloquentSpatial\Objects\Point;

class CurrentChallengesSeeder extends Seeder
{
    /**
     * Seed the current challenges from production database.
     */
    public function run(): void
    {
        $challenges = $this->getChallengesData();

        foreach ($challenges as $challengeData) {
            // Skip if challenge already exists
            if (Challenge::where('name', $challengeData['name'])->exists()) {
                $this->command->info("Challenge '{$challengeData['name']}' already exists, skipping...");
                continue;
            }

            // Create center point if exists
            $centerPoint = $challengeData['center_point']
                ? new Point($challengeData['center_point'][0], $challengeData['center_point'][1])
                : null;

            Challenge::create([
                'round_id' => $challengeData['round_id'],
                'name' => $challengeData['name'],
                'description' => $challengeData['description'],
                'type' => $challengeData['type'],
                'active' => $challengeData['active'],
                'is_active' => $challengeData['is_active'],
                'point' => null,
                'center_point' => $centerPoint,
            ]);

            $this->command->info("Created challenge: {$challengeData['name']}");
        }

        $this->command->info('Current challenges seeded successfully!');
    }

    private function getChallengesData(): array
    {
        return [
            [
                'round_id' => 1,
                'name' => 'Liman 3',
                'description' => 'Liman 3',
                'type' => 'ten_each',
                'active' => true,
                'is_active' => true,
                'center_point' => [19.839301977251957, 45.2387271765867],
            ],
            [
                'round_id' => 1,
                'name' => 'Limanski park',
                'description' => 'Limanski park',
                'type' => 'ten_each',
                'active' => true,
                'is_active' => true,
                'center_point' => [19.840522309818784, 45.238670702813494],
            ],
            [
                'round_id' => 1,
                'name' => 'Becarusa',
                'description' => 'Becarusa',
                'type' => 'ten_each',
                'active' => true,
                'is_active' => true,
                'center_point' => [19.85331294299553, 45.248388797090605],
            ],
            [
                'round_id' => 1,
                'name' => 'Ruski Kerestur',
                'description' => 'Ruski Kerestur',
                'type' => 'zigzag',
                'active' => true,
                'is_active' => true,
                'center_point' => [19.41293973928824, 45.55311777049541],
            ],
            [
                'round_id' => 1,
                'name' => 'Srem',
                'description' => 'Srem',
                'type' => 'zigzag',
                'active' => true,
                'is_active' => true,
                'center_point' => [19.240248489543298, 45.131236462043844],
            ],
        ];
    }
}

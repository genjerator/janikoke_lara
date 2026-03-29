<?php

namespace Database\Seeders;

use App\Models\Prize;
use Illuminate\Database\Seeder;

class PrizeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $prizes = $this->getPrizesData();

        foreach ($prizes as $prizeData) {
            // Skip if prize already exists
            if (Prize::where('name', $prizeData['name'])->exists()) {
                $this->command->info("Prize '{$prizeData['name']}' already exists, skipping...");
                continue;
            }

            Prize::create($prizeData);
            $this->command->info("✓ Created prize: {$prizeData['name']}");
        }

        $this->command->info('Prizes seeded successfully!');
    }

    private function getPrizesData(): array
    {
        return [
            [
                'name' => 'Beer Voucher',
                'amount' => 20,
                'cost' => 10,
                'status' => 1, // Active
                'description' => 'A voucher for a refreshing beer',
                'content' => '<p>Redeem this voucher for a cold beer at participating locations. Valid for 30 days from the date of issue.</p><p>Enjoy responsibly!</p>',
            ],
            [
                'name' => 'Craft Beer Prize',
                'amount' => 20,
                'cost' => 15,
                'status' => 1, // Active
                'description' => 'Local craft beer selection',
                'content' => '<p>Winner receives their choice of craft beer from our selection of local breweries.</p><ul><li>Valid at partner locations</li><li>Must be 18+ to redeem</li><li>Cannot be combined with other offers</li></ul>',
            ],
            [
                'name' => 'Beer Challenge Reward',
                'amount' => 20,
                'cost' => 20,
                'status' => 1, // Active
                'description' => 'Reward for completing a challenge',
                'content' => '<p>Congratulations on completing the challenge! Enjoy a well-deserved beer on us.</p><p>This prize can be redeemed at any time during your visit.</p>',
            ],
            [
                'name' => 'Weekend Beer Special',
                'amount' => 20,
                'cost' => 25,
                'status' => 2, // Pending
                'description' => 'Special weekend beer prize',
                'content' => '<h3>Weekend Special</h3><p>Available only on weekends, this prize gives you access to our premium beer selection.</p><p>Terms and conditions apply.</p>',
            ],
            [
                'name' => 'Beer Tasting Experience',
                'amount' => 20,
                'cost' => 50,
                'status' => 1, // Active
                'description' => 'A guided beer tasting experience',
                'content' => '<h2>Beer Tasting Experience</h2><p>Join us for a guided tasting of local and international beers. Learn about different styles, brewing techniques, and flavor profiles.</p><h3>What\'s Included:</h3><ul><li>Sampling of 4 different beers</li><li>Expert guide</li><li>Tasting notes</li><li>Snacks pairing</li></ul>',
            ],
        ];
    }
}


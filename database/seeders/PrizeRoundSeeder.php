<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PrizeRoundSeeder extends Seeder
{
    public function run(): void
    {
        $rounds = DB::table('rounds')->pluck('id');

        if ($rounds->isEmpty()) {
            $this->command->warn('No rounds found — skipping prize_round seeding.');
            return;
        }

        $prizes = DB::table('prizes')->get(['id', 'name', 'cost']);

        if ($prizes->isEmpty()) {
            $this->command->warn('No prizes found — run PrizeSeeder first.');
            return;
        }

        foreach ($rounds as $roundId) {
            foreach ($prizes as $prize) {
                $exists = DB::table('prize_round')
                    ->where('prize_id', $prize->id)
                    ->where('round_id', $roundId)
                    ->exists();

                if ($exists) {
                    $this->command->info("Skipping {$prize->name} / round {$roundId} (already linked)");
                    continue;
                }

                DB::table('prize_round')->insert([
                    'id'          => Str::uuid()->toString(),
                    'prize_id'    => $prize->id,
                    'round_id'    => $roundId,
                    'is_active'   => true,
                    'custom_cost' => null, // use prize's own cost
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);

                $this->command->info("✓ Linked '{$prize->name}' to round {$roundId}");
            }
        }

        $this->command->info('prize_round seeded successfully!');
    }
}

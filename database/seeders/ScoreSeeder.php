<?php

namespace Database\Seeders;

use App\Models\ChallengeArea;
use App\Models\Round;
use App\Models\Score;
use App\Models\User;
use Illuminate\Database\Seeder;

class ScoreSeeder extends Seeder
{
    /**
     * Seed dummy scores for user 1 in round 1.
     */
    public function run(): void
    {
        $user = User::find(1);
        $round = Round::find(1);

        if (!$user || !$round) {
            $this->command->warn('User 1 or Round 1 not found, skipping ScoreSeeder.');
            return;
        }

        $challengeAreas = ChallengeArea::whereHas('challenge', function ($query) use ($round) {
            $query->where('round_id', $round->id);
        })->with('area', 'challenge')->get();

        if ($challengeAreas->isEmpty()) {
            $this->command->warn('No challenge areas found for round 1, skipping ScoreSeeder.');
            return;
        }

        foreach ($challengeAreas as $challengeArea) {
            if (Score::where([
                'user_id' => $user->id,
                'round_id' => $round->id,
                'challenge_area_id' => $challengeArea->id,
            ])->exists()) {
                continue;
            }

            Score::create([
                'user_id' => $user->id,
                'round_id' => $round->id,
                'challenge_area_id' => $challengeArea->id,
                'amount' => 10,
                'status' => 1,
                'name' => $challengeArea->area->name,
                'description' => $challengeArea->challenge->description,
            ]);
        }

        $this->command->info('Dummy scores seeded for user 1, round 1.');
    }
}

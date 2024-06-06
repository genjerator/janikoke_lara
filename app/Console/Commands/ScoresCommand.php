<?php

namespace App\Console\Commands;

use App\Http\Services\ScoreService;
use App\Models\Score;
use App\Models\UserChallengeArea;
use Illuminate\Console\Command;

class ScoresCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scores:recalculate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recalculate scores based by user_challenge_area';

    /**
     * Execute the console command.
     */
    public function handle(ScoreService $scoreService)
    {
        Score::truncate();
        $UserAreaChallenges = UserChallengeArea::all();
        foreach ($UserAreaChallenges as $index => $UserAreaChallenge) {
            $this->info(
                sprintf(
                    "Recalculating the user(%d), allUserAreaChallenge (%d):",
                    $UserAreaChallenge->user->id,
                    $UserAreaChallenge->id));
            $scoreService->calculatePoints($UserAreaChallenge);
        }

    }
}

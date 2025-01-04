<?php

namespace App\Http\Services;

use App\Models\Round;
use App\Models\Score;
use App\Models\User;
use App\Models\UserChallengeArea;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ScoreService
{
    const DEFAULT_SCORE_AMOUNT = 10;

    public function calculatePoints(UserChallengeArea $userChallengeArea)
    {
        $user = $userChallengeArea->user;
        $challengeArea = $userChallengeArea->challengeArea;
        $challenge = $challengeArea->challenge;
        $round = $challenge->round;
        $area = $challengeArea->area;
        $score = new Score();
        $score->user_id = $user->id;
        $score->round_id = $round->id;
        $score->challenge_area_id = $challengeArea->id;
        $score->name = $area->name;
        $score->amount = self::DEFAULT_SCORE_AMOUNT;
        $score->description = $challenge->description;
        $score->save();
    }

    public function collectScoresForUserPerRound(Round $round, User $user): Collection
    {
        return Score::where(['user_id' => $user->id, 'round_id' => $round->id])->get();
    }

    public function collectScoresForUserPerRoundRaw(Round $round, User $user): Collection
    {
        $results = DB::table('scores as s')
            ->select(
                DB::raw('CONCAT(c.id, \'-\', a.id,\'-\',EXTRACT(EPOCH FROM s.created_at)::INT) as cidaid'),
                'c.id as challenge_id',
                'a.id as area_id',
                'c.name as challenge_name',
                'c.type as challenge_type',
                'c.description as challenge_description',
                'a.id as area_id',
                'a.name as area_name',
                's.amount as points',
                's.user_id as user_name',
                DB::raw('EXTRACT(EPOCH FROM s.created_at)::INT AS created_at_unix')
            )
            ->join('challenge_area as ca', 's.challenge_area_id', '=', 'ca.id')
            ->join('areas as a', 'a.id', '=', 'ca.area_id')
            ->join('challenges as c', 'c.id', '=', 'ca.challenge_id')
            ->where("s.user_id", $user->id)
            ->get();

        return $results->keyBy('cidaid');
    }

    public function collectScoresPerRoundGroupedByUser(Round $round): array
    {
        $usersWithScores = Score::with('user')
            ->where('round_id', $round->id)
            ->get()
            ->map(function ($score) {
                return [
                    'score_id' => $score->id,
                    'score_name' => $score->name,
                    'user_id' => $score->user_id,
                    'user_name' => $score->user->name,
                    'round_id' => $score->round_id,
                    'created_at' => $score->created_at->diffForHumans(),
                    'amount' => $score->amount, // Adjust field names based on your schema
                    //'user' => $score->user,  // Includes the related user data
                ];
            })
            ->groupBy('user_name')->toArray(); // Re-key the collection by user_id
// Step 1: Calculate totals
        $result = array_reduce(array_keys($usersWithScores), function ($acc, $key) use ($usersWithScores) {
            $values = $usersWithScores[$key];
            $total = array_reduce($values, function ($acc2, $score) {
                return $acc2 + $score['amount'];
            }, 0);

            // Add the total to the values and store in accumulator

            $acc[$key] = ['items' => $values, 'total' => $total];

            return $acc;
        }, []);

// Step 2: Sort by total in descending order
        uasort($result, function ($a, $b) {
            return $b['total'] <=> $a['total'];
        });
        return $result;
    }
}


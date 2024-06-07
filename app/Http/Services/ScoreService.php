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
    const DEFAULT_SCORE_AMOUNT= 10;
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

    public function collectScoresForUserPerRound(Round $round, User $user): Collection{
        return Score::where(['user_id'=>$user->id,'round_id'=>$round->id])->get();
    }

    public function collectScoresForUserPerRoundRaw(Round $round, User $user): Collection{
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
                DB::raw('EXTRACT(EPOCH FROM s.created_at)::INT AS created_at_unix')
            )
            ->join('challenge_area as ca', 's.challenge_area_id', '=', 'ca.id')
            ->join('areas as a', 'a.id', '=', 'ca.area_id')
            ->join('challenges as c', 'c.id', '=', 'ca.challenge_id')
            ->where("s.user_id",$user->id)
            ->get();

        return $results->keyBy('cidaid');
    }
}


<?php

namespace App\Http\Services;

use App\Models\Area;
use App\Models\Challenge;
use App\Models\ChallengeArea;
use App\Models\User;
use App\Models\UserChallengeArea;

class InsideAreaService
{
    public function proccess(User $user, Challenge $challenge, Area $area): void
    {
        $challengeArea = ChallengeArea::where(['area_id' => $area->id, 'challenge_id' => $challenge->id])->first();

        UserChallengeArea::firstOrCreate(
            ['user_id' => request('user_id')],
            ['challenge_area_id' => $challengeArea->id]
        );
    }
}

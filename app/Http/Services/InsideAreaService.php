<?php

namespace App\Http\Services;

use App\Enums\ChallengeTypeEnum;
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
        if ($challenge->type === ChallengeTypeEnum::TenEach->value) {
            UserChallengeArea::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'challenge_area_id' => $challengeArea->id
                ]
            );
        } else if ($challenge->type === ChallengeTypeEnum::Zigzag->value) {
            $last = UserChallengeArea::where(
                [
                    'user_id' => $user->id,
                ]
            )->orderBy('created_at', 'desc')->first();

            if (($last->challenge_area_id??0) !== $challengeArea->id) {
                UserChallengeArea::create(
                    [
                        'user_id' => $user->id,
                        'challenge_area_id' => $challengeArea->id
                    ]
                );
            }
            dd($last->challenge_area_id??0, $challengeArea->id);
        }

    }
}

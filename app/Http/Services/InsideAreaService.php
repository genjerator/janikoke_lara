<?php

namespace App\Http\Services;

use App\Enums\ChallengeTypeEnum;
use App\Http\Resources\ChallengeResource;
use App\Models\Area;
use App\Models\Challenge;
use App\Models\ChallengeArea;
use App\Models\Round;
use App\Models\User;
use App\Models\UserChallengeArea;
use Illuminate\Support\Facades\DB;

class InsideAreaService
{
    public function proccess(User $user, Challenge $challenge, Area $area): bool
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

            if (($last->challenge_area_id ?? 0) !== $challengeArea->id) {
                UserChallengeArea::create(
                    [
                        'user_id' => $user->id,
                        'challenge_area_id' => $challengeArea->id
                    ]
                );
                return true;
            }

        }
        return false;
    }

//    public function isUserInsidePolygon(User $user, Area $area, Challenge $challenge)
//    {
//        $challengeArea = UserChallengeArea::where(
//            [
//                'area_id' => $area->id,
//                'challenge_id' => $challenge->id,
//                'user_id' => $challenge->id,
//            ]
//        )->first();
//    }

    public function roundResults(Round $round, User $user)
    {
        $results = DB::table('challenges as c')
            ->select(
                DB::raw('CONCAT(c.id, \'-\', a.id) as cidaid'),
                'c.id as challenge_id',
                'a.id as area_id',
                'c.name as challenge_name',
                'c.type as challenge_type',
                'c.description as challenge_description',
                'a.id as area_id',
                'a.name as area_name',
                DB::raw('CAST(EXTRACT(EPOCH FROM uca.created_at) AS INTEGER) as time')
            )
            ->join('challenge_area as ca', 'ca.challenge_id', '=', 'c.id')
            ->join('user_challenge_area as uca', 'uca.challenge_area_id', '=', 'ca.id')
            ->join('areas as a', 'a.id', '=', 'ca.area_id')
            ->where('uca.user_id', $user->id)
            ->orderBy('c.id', 'asc')
            ->orderBy('time', 'desc')
            ->get();

        return $results->keyBy('cidaid');
    }

    public function allRoundResults(Round $round)
    {
        $results = DB::table('challenges as c')
            ->select(
                DB::raw('CONCAT(c.id, \'-\', a.id) as cidaid'),
                'c.id as challenge_id',
                'a.id as area_id',
                'c.name as challenge_name',
                'c.type as challenge_type',
                'c.description as challenge_description',
                'a.id as area_id',
                'a.name as area_name',
                'uca.id as id'
            )
            ->join('challenge_area as ca', 'ca.challenge_id', '=', 'c.id')
            ->join('user_challenge_area as uca', 'uca.challenge_area_id', '=', 'ca.id')
            ->join('areas as a', 'a.id', '=', 'ca.area_id')
            ->get();

        return $results->keyBy('id');
    }

    public function getResults(Round $round, User $user)
    {
        $results = $this->roundResults($round, $user)->toArray();
        $points = array_map(function ($item) {
            $item->points = 10;
            return $item;
        }, $results);

        return $points;
    }

    public function getRawResults(Round $round)
    {
        return $this->allRoundResults($round);
    }


    public function allChallengesAreasPerRound($round)
    {
        $results = DB::table('user_challenge_area as uca')
            ->rightJoin('challenge_area as ca', 'ca.id', '=', 'uca.challenge_area_id')
            ->rightJoin('challenges as c', 'c.id', '=', 'ca.challenge_id')
            ->rightJoin('areas as a', 'a.id', '=', 'ca.area_id')
            ->select(
                DB::raw('concat(c.id,\'-\',a.id) as cidaid'),
                'c.id as id',
                'c.name as name',
                'c.type as type',
                'c.description as description',
                'a.id as area_id',
                'a.name as area_name',
                'a.description as area_description',
                DB::raw('ST_AsGeoJSON(a.area) as polygons')
            )
            ->get();

        $results->map(function ($result) {
            $geojson = json_decode($result->polygons, true);
            $coordinates = $geojson['coordinates'][0]; // Get the coordinates of the polygon

            $points = [];
            foreach ($coordinates as $coordinate) {
                $points[] = [
                    'latitude' => $coordinate[1], // Latitude
                    'longitude' => $coordinate[0], // Longitude
                ];
            }
            $areas = [
                'id' => $result->area_id,
                'name' => $result->area_name,
                'description' => $result->area_description,
                'polygons' => $points,
            ];
            $result->areas = $areas;
//            dd($result->keyBy('cidaid'));
            unset($result->area_id, $result->area_name, $result->area_description, $result->polygons);
            return $result;
        });

        return $results->groupBy('id');
    }

    public function mix(Round $round, User $user)
    {
        $allActivChallengesPerRound = ChallengeResource::collection($round->activeChallenges);
        $results = $this->roundResults($round, $user);
        $allChallenges = json_decode($allActivChallengesPerRound->toJson(), true);
        $allChallenges = array_map(function ($challenge) use ($results) {
            $challenge['areas'] = $this->updateAreaZigzag($challenge['areas'], $challenge['id'], $results);

            return $challenge;
        }, $allChallenges);
        return $allChallenges;
    }

    public function updateArea(array &$areas, $challengeId, $results): array
    {
        $areas = array_map(function ($area) use ($results, $challengeId) {
            if ($results->has($challengeId . '-' . $area['id'])) {
                $area['status'] = 1;
            } else {
                $area['status'] = 0;
            }

            return $area;
        }, $areas);
        return $areas;
    }

    public function updateAreaZigzag(array &$areas, $challengeId, $results): array
    {

        $grouped = $results->groupBy('challenge_id');
        $grouped = $grouped->map(function ($challenges) {
            return $challenges->values()->map(function ($challenge, $index) {
                $challenge->status = $index === 0 ? 1 : 0;
                return $challenge;
            });
        });

        $grouped = $grouped->flatten(1)->keyBy('cidaid');

        $areas = array_map(function ($area) use ($grouped, $challengeId) {
            $r = $grouped->get($challengeId . '-' . $area['id']);
            $area['status'] = $r->status ?? 0;
            return $area;
        },$areas);

        return $areas;
    }
}


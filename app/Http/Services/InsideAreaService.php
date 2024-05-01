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

            if (($last->challenge_area_id ?? 0) !== $challengeArea->id) {
                UserChallengeArea::create(
                    [
                        'user_id' => $user->id,
                        'challenge_area_id' => $challengeArea->id
                    ]
                );
            }
            dd($last->challenge_area_id ?? 0, $challengeArea->id);
        }

    }

    public function isUserInsidePolygon(User $user, Area $area, Challenge $challenge)
    {
        $challengeArea = UserChallengeArea::where(
            [
                'area_id' => $area->id,
                'challenge_id' => $challenge->id,
                'user_id' => $challenge->id,
            ]
        )->first();
    }

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
                'a.name as area_name'
            )
            ->join('challenge_area as ca', 'ca.challenge_id', '=', 'c.id')
            ->join('user_challenge_area as uca', 'uca.challenge_area_id', '=', 'ca.id')
            ->join('areas as a', 'a.id', '=', 'ca.area_id')
            ->where('uca.user_id', $user->id)
            ->get();

        return $results->keyBy('cidaid');
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
        $tt = ChallengeResource::collection($round->challenges);
        $results = $this->roundResults($round, $user);
        $allChallenges = json_decode($tt->toJson(), true);
        $allChallenges = array_map(function ($challenge) use ($results) {
            $challenge['areas'] = $this->updateArea($challenge['areas'], $challenge['id'], $results);

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
}


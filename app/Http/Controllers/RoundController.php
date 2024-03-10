<?php

namespace App\Http\Controllers;

use App\Http\Requests\InsidePolygonRequest;
use App\Http\Resources\ChallengeResource;
use App\Http\Services\InsideAreaService;
use App\Models\Area;
use App\Models\Challenge;
use App\Models\ChallengeArea;
use App\Models\Round;
use App\Models\User;
use App\Models\UserChallengeArea;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class RoundController extends Controller
{
    public function __construct(private InsideAreaService $insideAreaService)
    {

    }

    public function challenges(Round $round)
    {
        return ChallengeResource::collection($round->challenges);
    }

    public function insidePolygon(InsidePolygonRequest $request, Round $round): JsonResponse
    {
        $user = User::findOrFail(request('user_id'));
        $challenge = Challenge::findOrFail(request('challenge_id'));
        $area = Area::findOrFail(request('area_id'));
        $this->insideAreaService->proccess($user, $challenge, $area);

        return new JsonResponse('', Response::HTTP_CREATED);
    }

}

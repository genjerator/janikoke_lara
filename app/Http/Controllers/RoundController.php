<?php

namespace App\Http\Controllers;

use App\Http\Requests\InsidePolygonRequest;
use App\Http\Services\InsideAreaService;
use App\Models\Area;
use App\Models\Challenge;
use App\Models\Round;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class RoundController extends Controller
{
    public function __construct(private InsideAreaService $insideAreaService)
    {

    }

    public function challenges(Round $round)
    {
        $user = User::find(1);
        $mix = $this->insideAreaService->mix($round, $user);
        return new JsonResponse($mix, Response::HTTP_OK);
    }

    public function uchallenges(Round $round)
    {
        $user = User::find(1);
        $roundResult = $this->insideAreaService->roundResults($round, $user);

        return new JsonResponse($roundResult);
    }

    public function insidePolygon(InsidePolygonRequest $request, Round $round): JsonResponse
    {
        $user = User::findOrFail(request('user_id'));
        $challenge = Challenge::findOrFail(request('challenge_id'));
        $area = Area::findOrFail(request('area_id'));
        $this->insideAreaService->proccess($user, $challenge, $area);

        return new JsonResponse('', Response::HTTP_CREATED);
    }

    public function roundResults(Round $round)
    {
        $user = User::findOrFail(1);
        return new JsonResponse($this->insideAreaService->getResults($round, $user), Response::HTTP_CREATED);
    }

    public function roundRawResults(Round $round)
    {
        $user = User::findOrFail(1);
        return new JsonResponse($this->insideAreaService->getRawResults($round, $user), Response::HTTP_CREATED);
    }

}

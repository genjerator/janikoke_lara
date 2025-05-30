<?php

namespace App\Http\Controllers;

use App\Http\Requests\InsidePolygonRequest;
use App\Http\Services\InsideAreaService;
use App\Models\Area;
use App\Models\Challenge;
use App\Models\Round;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoundController extends Controller
{
    public function __construct(private InsideAreaService $insideAreaService)
    {

    }

    public function challenges(Round $round)
    {
        $user = Auth::user();
        $mix = $this->insideAreaService->mix($round, $user);
        return new JsonResponse($mix, Response::HTTP_OK);
    }

    public function uchallenges(Round $round)
    {
        $user = User::findOrFail(request('user_id'));
        $roundResult = $this->insideAreaService->roundResults($round, $user);

        return new JsonResponse($roundResult);
    }

    public function insidePolygon(InsidePolygonRequest $request, Round $round): JsonResponse
    {
        $user = Auth::user();

//        $challenge = Challenge::findOrFail(request('challenge_id'));
//        $area = Area::findOrFail(request('area_id'));
        $ok = $this->insideAreaService->process($user, request('challenge_id'), request('area_id'));
        $message = ['status'=>$ok];
        return new JsonResponse($message, Response::HTTP_CREATED);
    }

    public function roundResults(Round $round)
    {
        $user = Auth::user();
        return new JsonResponse($this->insideAreaService->getResults($round, $user), Response::HTTP_CREATED);
    }

    public function roundRawResults(Round $round)
    {
        return new JsonResponse($this->insideAreaService->getRawResults($round), Response::HTTP_CREATED);
    }

}

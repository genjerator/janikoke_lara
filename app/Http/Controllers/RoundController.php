<?php

namespace App\Http\Controllers;

use App\Http\Requests\InsidePolygonRequest;
use App\Http\Resources\AreaArticleResource;
use App\Http\Services\InsideAreaService;
use App\Models\Area;
use App\Models\AreaArticle;
use App\Models\Challenge;
use App\Models\ChallengeArea;
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
        $ok = $this->insideAreaService->process($user, request('challenge_id'), request('area_id'));
        $message = ['status'=>$ok];
        return new JsonResponse($message, Response::HTTP_CREATED);
    }

    /**
     * All active articles for every area in this round. The mobile app loads
     * these in the background when a challenge opens and picks a random one to
     * show when the user steps into an area. Each item carries `area_id` so the
     * client can group/join them against the areas from challenges().
     */
    public function articles(Round $round): JsonResponse
    {
        $areaIds = ChallengeArea::whereIn('challenge_id', $round->challenges()->pluck('id'))
            ->pluck('area_id')
            ->unique()
            ->values();

        $articles = AreaArticle::whereIn('area_id', $areaIds)
            ->where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            })
            ->get();

        return new JsonResponse(AreaArticleResource::collection($articles), Response::HTTP_OK);
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

<?php

namespace App\Http\Controllers;

use App\Http\Resources\ScoreTotalResource;
use App\Http\Services\ScoreService;
use App\Models\Round;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ScoreController extends Controller
{
    public function __construct(private ScoreService $scoreService)
    {

    }
    public function roundScores(Round $round)
    {
        $user = Auth::user();
        $scores = $this->scoreService->collectScoresForUserPerRoundRaw($round, $user);
        return $scores;
    }

    public function toplist(Round $round)
    {
        $scores = $this->scoreService->collectScoresPerRoundGroupedByUser($round);
        return new JsonResponse($scores);
    }

    /**
     * Get total scores for authenticated user in a specific round.
     *
     * @param Round $round
     * @return JsonResponse
     */
    public function total(Round $round): JsonResponse
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        $total = $this->scoreService->getTotalScoreForUserInRound($round, $user);

        return ScoreTotalResource::make([
            'round_id' => $round->id,
            'user_id' => $user->id,
            'total' => $total,
        ])->response();
    }
}

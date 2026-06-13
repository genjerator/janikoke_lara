<?php

namespace App\Http\Controllers;

use App\Http\Resources\ScoreTotalResource;
use App\Http\Services\ScoreService;
use App\Services\ScoreService as BalanceService;
use App\Models\Round;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ScoreController extends Controller
{
    public function __construct(
        private ScoreService $scoreService,
        private BalanceService $balanceService,
    ) {

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

        // Use the same global available-balance the redemption flow enforces,
        // so the displayed total can never diverge from what a user can redeem.
        $total = $this->balanceService->getAvailableScores($user);

        return ScoreTotalResource::make([
            'round_id' => $round->id,
            'user_id' => $user->id,
            'total' => $total,
        ])->response();
    }
}

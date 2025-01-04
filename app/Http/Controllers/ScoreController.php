<?php

namespace App\Http\Controllers;

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
}

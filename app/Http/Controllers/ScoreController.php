<?php

namespace App\Http\Controllers;

use App\Http\Requests\InsidePolygonRequest;
use App\Http\Resources\ScoreResource;
use App\Http\Services\InsideAreaService;
use App\Http\Services\ScoreService;
use App\Models\Area;
use App\Models\Challenge;
use App\Models\Round;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

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
}

<?php

namespace App\Http\Controllers;

use App\Http\Resources\ScoreResource;
use App\Http\Services\ScoreService;
use App\Models\Round;
use App\Models\Score;
use Inertia\Inertia;

class ToplistController extends Controller
{
    public function __construct(private ScoreService $scoreService)
    {

    }

    public function index(Round $round)
    {
        $scores = $this->scoreService->collectScoresPerRoundGroupedByUser($round);
        //$scores = ScoreResource::collection($scores);
        //dd($scores->toJson());
        return Inertia::render('Toplist', ["scores" => $scores]);
    }
}

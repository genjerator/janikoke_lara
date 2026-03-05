<?php

namespace App\Http\Controllers;

use App\Http\Resources\ScoreResource;
use App\Http\Services\ScoreService;
use App\Models\Round;
use App\Models\Score;
use App\Models\User;
use Illuminate\Support\Facades\DB;
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

    public function ranking()
    {
        // Get all-time user rankings with most scores collected
        $rankings = User::select(
            'users.id',
            'users.name',
            'users.email',
            DB::raw('COUNT(scores.id) as score_count'),
            DB::raw('SUM(scores.amount) as total_amount'),
            DB::raw('AVG(scores.amount) as avg_amount')
        )
            ->leftJoin('scores', 'users.id', '=', 'scores.user_id')
            ->groupBy('users.id', 'users.name', 'users.email')
            ->orderBy('total_amount', 'desc')
            ->orderBy('score_count', 'desc')
            ->get()
            ->map(function ($user, $index) {
                return [
                    'rank' => $index + 1,
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'score_count' => $user->score_count ?? 0,
                    'total_amount' => $user->total_amount ?? 0,
                    'avg_amount' => round((float)($user->avg_amount ?? 0), 2),
                ];
            });

        return Inertia::render('Ranking', [
            'rankings' => $rankings,
        ]);
    }

    public function rankingLast30Days()
    {
        // Get last 30 days user rankings
        $rankings = User::select(
            'users.id',
            'users.name',
            'users.email',
            DB::raw('COUNT(scores.id) as score_count'),
            DB::raw('SUM(scores.amount) as total_amount'),
            DB::raw('AVG(scores.amount) as avg_amount')
        )
            ->leftJoin('scores', 'users.id', '=', 'scores.user_id')
            ->where('scores.created_at', '>=', now()->subDays(30))
            ->groupBy('users.id', 'users.name', 'users.email')
            ->orderBy('total_amount', 'desc')
            ->orderBy('score_count', 'desc')
            ->get()
            ->map(function ($user, $index) {
                return [
                    'rank' => $index + 1,
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'score_count' => $user->score_count ?? 0,
                    'total_amount' => $user->total_amount ?? 0,
                    'avg_amount' => round((float)($user->avg_amount ?? 0), 2),
                ];
            });

        return Inertia::render('Ranking', [
            'rankings' => $rankings,
            'period' => 'last30days',
        ]);
    }
}

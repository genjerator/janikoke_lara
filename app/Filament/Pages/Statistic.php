<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Score;
use App\Models\User;
use App\Models\Area;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Statistic extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static string $view = 'filament.pages.statistic';

    public function getScoresPerDay()
    {
        return Score::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as count'),
            DB::raw('SUM(amount) as total_amount')
        )
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date', 'desc')
            ->get();
    }

    public function getScoresPerUser()
    {
        return Score::select(
            'users.id',
            'users.name',
            DB::raw('COUNT(scores.id) as score_count'),
            DB::raw('SUM(scores.amount) as total_amount')
        )
            ->join('users', 'scores.user_id', '=', 'users.id')
            ->where('scores.created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('users.id', 'users.name')
            ->orderBy('total_amount', 'desc')
            ->limit(10)
            ->get();
    }

    public function getScoresPerArea()
    {
        return Score::select(
            'areas.id',
            'areas.name',
            DB::raw('COUNT(scores.id) as score_count'),
            DB::raw('SUM(scores.amount) as total_amount')
        )
            ->join('challenge_area', 'scores.challenge_area_id', '=', 'challenge_area.id')
            ->join('areas', 'challenge_area.area_id', '=', 'areas.id')
            ->where('scores.created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('areas.id', 'areas.name')
            ->orderBy('total_amount', 'desc')
            ->get();
    }

    public function getAreasWithChallenges()
    {
        return Score::select(
            'areas.id',
            'areas.name',
            'challenges.name as challenge_name',
            DB::raw('COUNT(scores.id) as score_count'),
            DB::raw('SUM(scores.amount) as total_amount')
        )
            ->join('challenge_area', 'scores.challenge_area_id', '=', 'challenge_area.id')
            ->join('areas', 'challenge_area.area_id', '=', 'areas.id')
            ->join('challenges', 'challenge_area.challenge_id', '=', 'challenges.id')
            ->where('scores.created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('areas.id', 'areas.name', 'challenges.id', 'challenges.name')
            ->orderBy('areas.name', 'asc')
            ->orderBy('challenges.name', 'asc')
            ->get();
    }

    public function getScoresPerUserPerDay()
    {
        return Score::select(
            'users.id',
            'users.name',
            DB::raw('DATE(scores.created_at) as date'),
            DB::raw('COUNT(scores.id) as score_count'),
            DB::raw('SUM(scores.amount) as total_amount')
        )
            ->join('users', 'scores.user_id', '=', 'users.id')
            ->where('scores.created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('users.id', 'users.name', DB::raw('DATE(scores.created_at)'))
            ->orderBy('users.name', 'asc')
            ->orderBy('date', 'asc')
            ->get();
    }

    public function getChartData()
    {
        $data = $this->getScoresPerUserPerDay();

        // Group data by user
        $groupedByUser = $data->groupBy('name');

        // Get all unique dates for x-axis
        $allDates = $data->pluck('date')->unique()->sort()->values();

        // Colors for different users
        $colors = [
            '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF',
            '#FF9F40', '#FF6384', '#C9CBCF', '#4BC0C0', '#FF6384'
        ];

        $datasets = [];
        $colorIndex = 0;

        foreach ($groupedByUser as $userName => $userScores) {
            $scoresByDate = $userScores->keyBy('date');

            $data = [];
            foreach ($allDates as $date) {
                $data[] = $scoresByDate->get($date)?->total_amount ?? 0;
            }

            $datasets[] = [
                'label' => $userName,
                'data' => $data,
                'borderColor' => $colors[$colorIndex % count($colors)],
                'backgroundColor' => str_replace(')', ', 0.1)', str_replace('rgb', 'rgba', $colors[$colorIndex % count($colors)])),
                'borderWidth' => 2,
                'tension' => 0.4,
                'fill' => true,
            ];

            $colorIndex++;
        }

        return [
            'labels' => $allDates->toArray(),
            'datasets' => $datasets,
        ];
    }

    public function getTotalStats()
    {
        $thirtyDaysAgo = Carbon::now()->subDays(30);

        $totalScores = Score::where('created_at', '>=', $thirtyDaysAgo)->count();
        $totalAmount = Score::where('created_at', '>=', $thirtyDaysAgo)->sum('amount') ?? 0;
        $totalUsers = Score::where('created_at', '>=', $thirtyDaysAgo)->distinct()->pluck('user_id')->count();

        $totalAreas = DB::table('scores')
            ->join('challenge_area', 'scores.challenge_area_id', '=', 'challenge_area.id')
            ->where('scores.created_at', '>=', $thirtyDaysAgo)
            ->distinct('challenge_area.area_id')
            ->count('challenge_area.area_id');

        return [
            'total_scores' => $totalScores,
            'total_amount' => $totalAmount,
            'total_users' => $totalUsers,
            'total_areas' => $totalAreas,
        ];
    }
}

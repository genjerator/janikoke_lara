<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PrizeResource;
use App\Models\Prize;
use App\Models\Round;
use Illuminate\Http\JsonResponse;

class PrizeController extends Controller
{
    /**
     * List available prizes for a specific round.
     *
     * @param int $round Round ID
     * @return JsonResponse
     */
    public function index(int $round): JsonResponse
    {
        $roundModel = Round::find($round);

        if (!$roundModel) {
            return response()->json([
                'success' => false,
                'message' => 'Round not found',
            ], 404);
        }

        // Get prizes linked to this round through prize_round table
        $prizes = $roundModel->prizes()
            ->where('prizes.status', 1) // Prize must be active
            ->where('prize_round.is_active', true) // Must be active in this round
            ->orderBy('prize_round.custom_cost')
            ->orderBy('prizes.cost')
            ->get();

        return response()->json([
            'success' => true,
            'round_id' => $round,
            'data' => PrizeResource::collection($prizes),
        ]);
    }
}

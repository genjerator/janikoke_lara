<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PrizeRedemptionResource;
use App\Http\Resources\PrizeResource;
use App\Models\Prize;
use App\Models\PrizeRedemption;
use App\Models\Round;
use App\Services\ScoreService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PrizeController extends Controller
{
    public function __construct(private readonly ScoreService $scoreService)
    {
    }

    /**
     * List available prizes for a specific round (legacy endpoint — public).
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

        $prizes = $roundModel->prizes()
            ->where('prizes.status', 1)
            ->where('prize_round.is_active', true)
            ->orderBy('prize_round.custom_cost')
            ->orderBy('prizes.cost')
            ->get();

        return response()->json([
            'success' => true,
            'round_id' => $round,
            'data' => PrizeResource::collection($prizes),
        ]);
    }

    /**
     * List all active prizes with the authenticated user's score balance.
     *
     * GET /api/prizes
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function listPrizes(Request $request): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $totalScores = $this->scoreService->getTotalScores($user);
        $availableScores = $this->scoreService->getAvailableScores($user);
        $spentScores = $totalScores - $availableScores;

        $prizes = Prize::where('status', 1)
            ->orderBy('cost')
            ->get();

        $prizeCollection = PrizeResource::collection($prizes)
            ->additional(['available_scores' => $availableScores]);

        return response()->json([
            'success' => true,
            'user' => [
                'total_scores' => $totalScores,
                'available_scores' => $availableScores,
                'spent_scores' => $spentScores,
            ],
            'data' => $prizeCollection,
        ]);
    }

    /**
     * Redeem a prize for the authenticated user.
     *
     * POST /api/prizes/redeem
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function redeem(Request $request): JsonResponse
    {
        $request->validate([
            'prize_id' => ['required', 'string', 'exists:prizes,id'],
        ]);

        /** @var \App\Models\User $user */
        $user = $request->user();

        try {
            $redemption = DB::transaction(function () use ($request, $user) {
                // Re-check available scores inside transaction to prevent race conditions
                $availableScores = $this->scoreService->getAvailableScores($user);

                $prize = Prize::where('id', $request->prize_id)
                    ->where('status', 1)
                    ->lockForUpdate()
                    ->first();

                if (!$prize) {
                    abort(404, 'Prize not found or not available');
                }

                if ($availableScores < $prize->cost) {
                    abort(422, 'Insufficient scores to redeem this prize');
                }

                return PrizeRedemption::create([
                    'user_id' => $user->id,
                    'prize_id' => $prize->id,
                    'prize_name' => $prize->name,
                    'prize_amount' => $prize->amount,
                    'score_cost' => $prize->cost,
                    'status' => 'approved',
                    'redemption_code' => PrizeRedemption::generateCode($prize->name),
                    'redeemed_at' => now(),
                    'approved_at' => now(),
                ]);
            });
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], $e->getStatusCode());
        }

        $newAvailableScores = $this->scoreService->getAvailableScores($user);

        return response()->json([
            'success' => true,
            'redemption' => PrizeRedemptionResource::make($redemption),
            'new_balance' => [
                'available_scores' => $newAvailableScores,
            ],
        ], 201);
    }

    /**
     * Get the authenticated user's redemption history (paginated).
     *
     * GET /api/prizes/redemptions
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function redemptions(Request $request): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $redemptions = PrizeRedemption::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => PrizeRedemptionResource::collection($redemptions),
            'meta' => [
                'current_page' => $redemptions->currentPage(),
                'last_page' => $redemptions->lastPage(),
                'per_page' => $redemptions->perPage(),
                'total' => $redemptions->total(),
            ],
        ]);
    }

    /**
     * Get a single redemption for the authenticated user.
     *
     * GET /api/prizes/redemptions/{id}
     *
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     */
    public function showRedemption(Request $request, string $id): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $redemption = PrizeRedemption::find($id);

        if (!$redemption) {
            return response()->json([
                'success' => false,
                'message' => 'Redemption not found',
            ], 404);
        }

        if ((string) $redemption->user_id !== (string) $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => PrizeRedemptionResource::make($redemption),
        ]);
    }
}

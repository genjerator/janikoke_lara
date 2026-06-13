<?php

namespace App\Services;

use App\Models\User;
use App\Models\Score;
use App\Models\PrizeRedemption;

class ScoreService
{
    /**
     * Get total scores earned by a user.
     *
     * @param User|int $user User model or user ID
     * @param bool $activeOnly Only count scores with status = 1
     * @return int
     */
    public function getTotalScores(User|int $user, bool $activeOnly = true): int
    {
        $userId = $user instanceof User ? $user->id : $user;

        $query = Score::where('user_id', $userId);

        if ($activeOnly) {
            $query->where('status', 1);
        }

        return $query->sum('amount');
    }

    /**
     * Get total scores earned by a user for a specific round.
     *
     * @param User|int $user User model or user ID
     * @param int $roundId Round ID
     * @param bool $activeOnly Only count scores with status = 1
     * @return int
     */
    public function getTotalScoresByRound(User|int $user, int $roundId, bool $activeOnly = true): int
    {
        $userId = $user instanceof User ? $user->id : $user;

        $query = Score::where('user_id', $userId)
            ->where('round_id', $roundId);

        if ($activeOnly) {
            $query->where('status', 1);
        }

        return $query->sum('amount');
    }

    /**
     * Get available scores (earned minus spent on prize redemptions).
     *
     * @param User|int $user User model or user ID
     * @return int
     */
    public function getAvailableScores(User|int $user): int
    {
        $userId = $user instanceof User ? $user->id : $user;

        // Available = everything earned minus the exact cost of past redemptions.
        // We do NOT consume whole score rows: scores are atomic 10-point records,
        // so flipping rows over-spends whenever a prize cost is not a multiple of
        // the score amount (e.g. a 15-pt prize would burn two 10-pt rows = 20).
        $earned = Score::where('user_id', $userId)->sum('amount');
        $spent = PrizeRedemption::where('user_id', $userId)->sum('score_cost');

        return (int) ($earned - $spent);
    }

    /**
     * Get user's score breakdown by round.
     *
     * @param User|int $user User model or user ID
     * @return array Array of rounds with their scores
     */
    public function getScoreBreakdownByRound(User|int $user): array
    {
        $userId = $user instanceof User ? $user->id : $user;

        return Score::where('user_id', $userId)
            ->where('status', 1)
            ->selectRaw('round_id, SUM(amount) as total')
            ->groupBy('round_id')
            ->get()
            ->map(fn($score) => [
                'round_id' => $score->round_id,
                'total' => $score->total,
            ])
            ->toArray();
    }

    /**
     * Check if user has enough scores to redeem a prize.
     *
     * @param User|int $user User model or user ID
     * @param int $requiredScores Score points required
     * @return bool
     */
    public function hasEnoughScores(User|int $user, int $requiredScores): bool
    {
        return $this->getAvailableScores($user) >= $requiredScores;
    }
}

# Score Service

Service for calculating and managing user scores.

## Usage

### Method 1: Using ScoreService directly

```php
use App\Services\ScoreService;

$scoreService = app(ScoreService::class);

// Get total scores for a user
$total = $scoreService->getTotalScores($user);
$total = $scoreService->getTotalScores($userId); // Can also pass user ID

// Get available scores (earned minus spent)
$available = $scoreService->getAvailableScores($user);

// Get scores for specific round
$roundScores = $scoreService->getTotalScoresByRound($user, $roundId);

// Check if user has enough scores
$canRedeem = $scoreService->hasEnoughScores($user, 50);

// Get score breakdown by round
$breakdown = $scoreService->getScoreBreakdownByRound($user);
// Returns: [['round_id' => 1, 'total' => 100], ['round_id' => 2, 'total' => 50]]
```

### Method 2: Using User model methods (recommended)

```php
$user = User::find(1);

// Get total scores
$total = $user->getTotalScores();

// Get available scores
$available = $user->getAvailableScores();

// Check if user can afford a prize
if ($user->hasEnoughScores($prize->cost)) {
    // User can redeem this prize
}

// Get breakdown by round
$breakdown = $user->getScoreBreakdownByRound();
```

## API Methods

### `getTotalScores(User|int $user, bool $activeOnly = true): int`
Returns the sum of all scores for a user.
- `$user`: User model or user ID
- `$activeOnly`: Only count scores with status = 1 (default: true)

### `getAvailableScores(User|int $user): int`
Returns available scores (earned minus spent on prize redemptions).
Note: Currently just returns total earned. Will subtract spent scores when redemption system is implemented.

### `getTotalScoresByRound(User|int $user, int $roundId, bool $activeOnly = true): int`
Returns total scores for a specific round.

### `hasEnoughScores(User|int $user, int $requiredScores): bool`
Checks if user has enough available scores.

### `getScoreBreakdownByRound(User|int $user): array`
Returns array of scores grouped by round.

## Examples

### Display user's score balance in Blade
```blade
<div>
    <h3>Your Score Balance</h3>
    <p>Total Earned: {{ auth()->user()->getTotalScores() }}</p>
    <p>Available: {{ auth()->user()->getAvailableScores() }}</p>
</div>
```

### Check before prize redemption
```php
$prize = Prize::find($prizeId);

if (!$user->hasEnoughScores($prize->cost)) {
    return back()->with('error', 'Not enough scores!');
}

// Process redemption...
```

### API endpoint
```php
public function getBalance(Request $request)
{
    $user = $request->user();

    return response()->json([
        'total_earned' => $user->getTotalScores(),
        'available' => $user->getAvailableScores(),
        'breakdown' => $user->getScoreBreakdownByRound(),
    ]);
}
```

## Future Enhancements

When prize redemption system is implemented, `getAvailableScores()` will be updated to:
```php
$totalEarned = $this->getTotalScores($user);
$totalSpent = PrizeRedemption::where('user_id', $userId)
    ->whereIn('status', ['pending', 'approved', 'completed'])
    ->sum('score_cost');
return $totalEarned - $totalSpent;
```

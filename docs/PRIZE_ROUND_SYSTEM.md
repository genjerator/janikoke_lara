# Prize-Round System

## Overview

The `prize_round` table creates a many-to-many relationship between prizes and rounds, allowing you to:
- Make specific prizes available only in certain rounds
- Override prize costs per round (e.g., discounts for special events)
- Control which prizes are active in each round independently

## Database Structure

### Table: `prize_round`

```sql
CREATE TABLE prize_round (
    id UUID PRIMARY KEY,
    prize_id UUID,              -- Links to prizes.id
    round_id INTEGER,           -- Links to rounds.id
    is_active BOOLEAN DEFAULT true,
    custom_cost INTEGER NULL,   -- Override prize.cost for this round
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    UNIQUE(prize_id, round_id)  -- One prize per round
);
```

### Fields

| Field | Type | Description |
|-------|------|-------------|
| `id` | UUID | Primary key |
| `prize_id` | UUID | Prize identifier (no FK constraint) |
| `round_id` | INTEGER | Round identifier (no FK constraint) |
| `is_active` | BOOLEAN | Is this prize active in this round? |
| `custom_cost` | INTEGER (nullable) | Override cost for this round. If null, uses `prizes.cost` |

## Use Cases

### 1. Round-Specific Prizes

Make certain prizes available only in specific rounds:

```php
// Make "Summer Beer" available only in Round 2
$prize = Prize::where('name', 'Summer Beer')->first();
$round = Round::find(2);

PrizeRound::create([
    'prize_id' => $prize->id,
    'round_id' => $round->id,
    'is_active' => true,
]);
```

### 2. Special Pricing Per Round

Offer discounts or different pricing in different rounds:

```php
// Normal price: 50 points
// Round 1 special: 25 points (50% off)

PrizeRound::create([
    'prize_id' => $prize->id,
    'round_id' => 1,
    'is_active' => true,
    'custom_cost' => 25, // Override the default cost
]);
```

### 3. Temporarily Disable Prizes

Disable a prize in a specific round without deleting the relationship:

```php
PrizeRound::where('prize_id', $prize->id)
    ->where('round_id', $round->id)
    ->update(['is_active' => false]);
```

## Model Relationships

### Prize Model

```php
// Get all rounds this prize is available in
$rounds = $prize->rounds;

// Get active rounds only
$activeRounds = $prize->rounds()
    ->where('prize_round.is_active', true)
    ->get();

// Access pivot data
foreach ($prize->rounds as $round) {
    echo "Cost in {$round->name}: " . ($round->pivot->custom_cost ?? $prize->cost);
}
```

### Round Model

```php
// Get all prizes available in this round
$prizes = $round->prizes;

// Get active prizes only
$activePrizes = $round->prizes()
    ->where('prizes.status', 1)
    ->where('prize_round.is_active', true)
    ->get();

// Access pivot data
foreach ($round->prizes as $prize) {
    $effectiveCost = $prize->pivot->custom_cost ?? $prize->cost;
    echo "{$prize->name}: {$effectiveCost} points";
}
```

### PrizeRound Model

```php
// Get effective cost (custom or default)
$prizeRound = PrizeRound::find($id);
$cost = $prizeRound->getEffectiveCost();

// Access related models
$prize = $prizeRound->prize;
$round = $prizeRound->round;
```

## API Behavior

### Endpoint

`GET /api/prizes/{round}`

Returns only prizes that:
1. Have `prizes.status = 1` (Active)
2. Are linked to the specified round in `prize_round`
3. Have `prize_round.is_active = true`

The `cost` field in the response uses `custom_cost` if set, otherwise falls back to the prize's default cost.

**Example:**
```bash
# Get prizes for Round 1
curl http://your-domain.com/api/prizes/1
```

## Management in Filament

You can create a Filament resource to manage prize-round relationships:

```php
// In PrizeResource, add a relation manager
public static function getRelations(): array
{
    return [
        RelationManagers\RoundsRelationManager::class,
    ];
}
```

This allows admins to:
- View which rounds a prize is available in
- Add/remove rounds for a prize
- Set custom costs per round
- Enable/disable prizes per round

## Common Queries

### Get prizes for current round

```php
$currentRound = Round::where('is_active', true)->first();
$prizes = $currentRound->prizes()
    ->where('prizes.status', 1)
    ->where('prize_round.is_active', true)
    ->get();
```

### Check if prize is available in round

```php
$isAvailable = PrizeRound::where('prize_id', $prizeId)
    ->where('round_id', $roundId)
    ->where('is_active', true)
    ->exists();
```

### Get all rounds where prize has discount

```php
$discountedRounds = $prize->rounds()
    ->whereNotNull('prize_round.custom_cost')
    ->where('prize_round.custom_cost', '<', $prize->cost)
    ->get();
```

### Get effective cost for prize in round

```php
$prizeRound = PrizeRound::where('prize_id', $prizeId)
    ->where('round_id', $roundId)
    ->first();

$cost = $prizeRound ? $prizeRound->getEffectiveCost() : $prize->cost;
```

## Migration Example

When creating the table, always check if it exists (per CLAUDE.md):

```php
public function up(): void
{
    if (!Schema::hasTable('prize_round')) {
        Schema::create('prize_round', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('prize_id');
            $table->integer('round_id');
            $table->boolean('is_active')->default(true);
            $table->integer('custom_cost')->nullable();
            $table->timestamps();

            $table->index('prize_id');
            $table->index('round_id');
            $table->unique(['prize_id', 'round_id']);
        });
    }
}
```

## Seeding Example

```php
// PrizeRoundSeeder.php

$round1 = Round::where('name', 'Round1')->first();
$prizes = Prize::where('status', 1)->get();

foreach ($prizes as $prize) {
    // Make all active prizes available in Round 1
    PrizeRound::updateOrCreate(
        [
            'prize_id' => $prize->id,
            'round_id' => $round1->id,
        ],
        [
            'is_active' => true,
            'custom_cost' => null, // Use default cost
        ]
    );
}

// Add special discount for "Beer Voucher" in Round 1
$beerVoucher = Prize::where('name', 'Beer Voucher')->first();
PrizeRound::updateOrCreate(
    [
        'prize_id' => $beerVoucher->id,
        'round_id' => $round1->id,
    ],
    [
        'is_active' => true,
        'custom_cost' => 5, // Discounted from 10 to 5
    ]
);
```

## Best Practices

1. **Always use `custom_cost` for round-specific pricing**
   - Don't modify the prize's base cost
   - Use `custom_cost` in `prize_round` to override per round

2. **Check availability when redeeming**
   - Verify prize is linked to the active round
   - Verify `is_active = true` for the round

3. **Use effective cost calculation**
   - Always use `getEffectiveCost()` or check `custom_cost ?? prize.cost`
   - Never assume the base cost is what the user pays

4. **Soft disable, don't delete**
   - Use `is_active = false` to temporarily disable
   - Keep historical data for reporting

5. **Index for performance**
   - `prize_id` and `round_id` are indexed
   - Unique constraint prevents duplicates

## Future Enhancements

- **Stock/Inventory:** Add `quantity_available` per round
- **Date Ranges:** Add `available_from` and `available_until` for time-limited prizes
- **Redemption Limits:** Add `max_redemptions_per_user` per round
- **Priority/Sorting:** Add `display_order` for custom sorting in UI

## Notes

- Follows CLAUDE.md guidelines (UUID primary key, no foreign key constraints)
- Unique constraint ensures one entry per prize-round combination
- `custom_cost` is nullable - null means "use default cost"
- If a prize is NOT in `prize_round` for a round, it won't appear when filtering by that round

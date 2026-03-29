# Prize API Resource Refactor

**Date:** March 30, 2026
**Status:** ✅ Completed

---

## Overview

Refactored the Prize API endpoint to use Laravel's API Resource pattern instead of manual array mapping. This provides better maintainability, consistency, and follows Laravel best practices.

---

## Changes Made

### 1. Created PrizeResource

**File:** `app/Http/Resources/PrizeResource.php`

```php
<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class PrizeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'amount' => $this->amount,
            'cost' => $this->whenPivotLoaded('prize_round', function () {
                return $this->pivot->custom_cost ?? $this->cost;
            }, $this->cost),
            'content' => $this->content,
            'image_url' => $this->image
                ? Storage::disk('public')->url($this->image)
                : null,
        ];
    }
}
```

**Key Features:**
- ✅ Handles `image_url` generation
- ✅ Smart handling of `custom_cost` from pivot table
- ✅ Falls back to default cost if no custom cost set
- ✅ Returns `null` for image_url when no image
- ✅ Automatic pivot detection with `whenPivotLoaded()`

### 2. Updated PrizeController

**File:** `app/Http/Controllers/Api/PrizeController.php`

**Before:**
```php
$prizes = $roundModel->prizes()
    ->where('prizes.status', 1)
    ->where('prize_round.is_active', true)
    ->orderBy('prize_round.custom_cost')
    ->orderBy('prizes.cost')
    ->get()
    ->map(function ($prize) {
        return [
            'id' => $prize->id,
            'name' => $prize->name,
            'description' => $prize->description,
            'amount' => $prize->amount,
            'cost' => $prize->pivot->custom_cost ?? $prize->cost,
            'content' => $prize->content,
            'image_url' => $prize->image
                ? Storage::disk('public')->url($prize->image)
                : null,
        ];
    });

return response()->json([
    'success' => true,
    'round_id' => $round,
    'data' => $prizes,
]);
```

**After:**
```php
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
```

**Benefits:**
- ✅ Cleaner controller code
- ✅ Removed manual mapping logic
- ✅ Removed `Storage` import from controller
- ✅ Single responsibility - controller fetches data, resource formats it

---

## API Response (Unchanged)

The API response format remains exactly the same:

```json
{
  "success": true,
  "round_id": 1,
  "data": [
    {
      "id": "a169022b-146e-4117-8fcd-9d860256a00b",
      "name": "Beer Voucher",
      "description": "A voucher for a refreshing beer",
      "amount": 20,
      "cost": 5,
      "content": "<p>Redeem this voucher...</p>",
      "image_url": "http://localhost:88/storage/prizes/01KMXSZE8F1Y0GGWYHNBQ7SEPS.jpeg"
    }
  ]
}
```

---

## Custom Cost Handling

### How It Works

The `PrizeResource` uses Laravel's `whenPivotLoaded()` helper:

```php
'cost' => $this->whenPivotLoaded('prize_round', function () {
    return $this->pivot->custom_cost ?? $this->cost;
}, $this->cost),
```

**Logic:**
1. **If pivot table loaded** → Check `pivot->custom_cost`
   - If `custom_cost` is set → Use it
   - If `custom_cost` is null → Use default `cost`
2. **If no pivot table** → Use default `cost`

### Example

**Database:**
- Prize "Beer Voucher" default cost: 10 pts
- Round 1 custom cost for Beer Voucher: 5 pts

**API Response:**
```json
{
  "name": "Beer Voucher",
  "cost": 5
}
```

**Verified:** ✅ Custom cost correctly returned as 5 pts

---

## Benefits of Using API Resources

### 1. Reusability
```php
// Use in different endpoints
PrizeResource::make($prize);           // Single prize
PrizeResource::collection($prizes);    // Collection
```

### 2. Consistency
All prize responses use the same format across the application.

### 3. Testability
```php
// Easy to test resource independently
$resource = new PrizeResource($prize);
$array = $resource->toArray(request());
```

### 4. Maintainability
Change response format in one place, affects all endpoints.

### 5. Documentation
Resource class serves as documentation for API response structure.

---

## Testing

### Test Endpoint
```bash
curl http://localhost:88/api/prizes/1 | jq
```

### Verify Custom Cost
```bash
curl -s http://localhost:88/api/prizes/1 | \
  jq '.data[] | select(.name == "Beer Voucher") | {name, cost}'
```

**Expected:**
```json
{
  "name": "Beer Voucher",
  "cost": 5
}
```

### Verify Image URLs
```bash
curl -s http://localhost:88/api/prizes/1 | \
  jq '.data[] | {name, image_url}'
```

**Expected:**
```json
{
  "name": "Beer Voucher",
  "image_url": "http://localhost:88/storage/prizes/01KMXSZE8F1Y0GGWYHNBQ7SEPS.jpeg"
}
```

---

## Future Enhancements

### Additional Resources

Create related resources for consistency:

```php
// app/Http/Resources/RoundResource.php
class RoundResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'starts_at' => $this->starts_at,
            'ends_at' => $this->ends_at,
            'prizes' => PrizeResource::collection($this->whenLoaded('prizes')),
        ];
    }
}
```

### Conditional Fields

Add fields based on user authentication:

```php
public function toArray(Request $request): array
{
    return [
        'id' => $this->id,
        'name' => $this->name,
        // ... other fields

        // Only show to authenticated users
        'can_redeem' => $this->when(
            $request->user(),
            fn() => $request->user()->hasEnoughScores($this->cost)
        ),
    ];
}
```

### Nested Resources

Include related data:

```php
'redemptions' => PrizeRedemptionResource::collection(
    $this->whenLoaded('redemptions')
),
```

---

## Code Quality Improvements

### Before Refactor
- ❌ Logic mixed in controller
- ❌ Hard to test response format
- ❌ Hard to reuse in other endpoints
- ❌ Storage dependency in controller

### After Refactor
- ✅ Clean separation of concerns
- ✅ Easy to test resource independently
- ✅ Reusable across endpoints
- ✅ Controller focuses on data fetching
- ✅ Resource handles formatting

---

## Files Modified

1. **Created:** `app/Http/Resources/PrizeResource.php`
2. **Updated:** `app/Http/Controllers/Api/PrizeController.php`

---

## Backward Compatibility

✅ **100% backward compatible**
- Same endpoint URL
- Same response format
- Same HTTP methods
- No breaking changes

---

## Summary

Refactored Prize API to use Laravel API Resources following best practices:

- ✅ Created `PrizeResource` for response formatting
- ✅ Simplified controller logic
- ✅ Maintained exact same API response format
- ✅ Verified custom cost handling works correctly
- ✅ Verified image URLs work correctly
- ✅ All tests passing

**Result:** Cleaner, more maintainable code with no breaking changes.

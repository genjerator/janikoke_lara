# Prize Redemption System - Planning Document

**Date:** March 29, 2026
**Status:** Planning Phase

---

## Overview

Enable users to exchange their earned score points for prizes. Users accumulate scores by completing challenges, and can spend these scores to redeem prizes (e.g., beer vouchers).

---

## Current State

### Existing Tables & Data

**`prizes` table:**
- `id` (UUID) - Primary key
- `name` (string) - Prize name (e.g., "Beer Voucher")
- `description` (string) - Short description
- `content` (text) - HTML content with terms/details
- `amount` (integer) - Quantity/value of the prize (currently 20 for all beers)
- `cost` (integer) - How many score points needed to redeem (10, 15, 20, 25, 50)
- `status` (integer) - 0=Inactive, 1=Active, 2=Pending, 3=Awarded
- `created_at`, `updated_at`

**`scores` table:**
- `id` (integer) - Primary key
- `user_id` (integer) - Foreign key to users
- `challenge_area_id` (integer) - Foreign key to challenge_area
- `round_id` (integer) - Foreign key to rounds
- `amount` (integer) - Score points earned
- `status` (integer) - 1=Active
- `name`, `description` (string) - Optional metadata
- `created_at`, `updated_at`

**`users` table:**
- Standard Laravel users table
- Has relationship: `hasMany(Score::class)`

### Existing Services
- **`ScoreService`** - Calculates user's total scores
  - `getTotalScores($user)` - Sum of all earned scores
  - `getAvailableScores($user)` - Available to spend (currently same as total, will subtract spent scores)
  - `hasEnoughScores($user, $amount)` - Check if user can afford something

---

## Requirements

### Functional Requirements

1. **User can view available prizes** with their cost
2. **User can see their score balance** (earned vs available)
3. **User can redeem a prize** if they have enough scores
4. **System deducts scores** when redemption is successful
5. **User receives redemption code/voucher** to claim prize
6. **Admin can view/manage redemptions** in Filament
7. **Admin can approve/reject pending redemptions** (optional workflow)
8. **Prevent double-spending** of scores
9. **Track redemption history** per user

### Business Rules

1. Only **Active** prizes (status=1) can be redeemed
2. User must have `available_scores >= prize.cost`
3. Redemptions can have states:
   - **pending** - Awaiting admin approval (if approval workflow enabled)
   - **approved** - Admin approved, ready to use
   - **completed** - User claimed/used the prize
   - **cancelled** - Admin cancelled or user refunded
4. Cancelled redemptions should refund the scores
5. Each redemption generates a unique code for claiming

---

## Proposed Database Structure

### New Table: `prize_redemptions`

```php
Schema::create('prize_redemptions', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->uuid('user_id'); // No FK constraint (per CLAUDE.md)
    $table->uuid('prize_id'); // No FK constraint

    // Snapshot data at redemption time (in case prize changes later)
    $table->string('prize_name');
    $table->integer('prize_amount'); // e.g., 20 (for 20ml beer or whatever unit)
    $table->integer('score_cost'); // How many points were spent

    // Redemption details
    $table->string('status')->default('pending'); // pending, approved, completed, cancelled
    $table->string('redemption_code')->unique(); // e.g., "BEER-ABC123"
    $table->text('notes')->nullable(); // Admin notes or user message

    // Timestamps
    $table->timestamp('redeemed_at'); // When user initiated redemption
    $table->timestamp('approved_at')->nullable(); // When admin approved
    $table->timestamp('completed_at')->nullable(); // When user claimed
    $table->timestamp('cancelled_at')->nullable(); // If cancelled

    $table->timestamps();

    // Indexes for performance
    $table->index('user_id');
    $table->index('prize_id');
    $table->index('status');
    $table->index('redemption_code');
});
```

**Why snapshot prize data?**
- If prize name/amount changes in the future, we still know what the user redeemed
- Historical accuracy for reports

---

## System Flow

### 1. User Views Prizes (Frontend)

**Endpoint:** `GET /api/prizes` or page `/prizes`

**Response:**
```json
{
  "user": {
    "total_scores": 150,
    "available_scores": 100,
    "spent_scores": 50
  },
  "prizes": [
    {
      "id": "uuid",
      "name": "Beer Voucher",
      "description": "A voucher for a refreshing beer",
      "cost": 10,
      "amount": 20,
      "can_afford": true
    },
    {
      "id": "uuid",
      "name": "Beer Tasting Experience",
      "description": "A guided beer tasting experience",
      "cost": 50,
      "amount": 20,
      "can_afford": true
    }
  ]
}
```

### 2. User Redeems Prize

**Endpoint:** `POST /api/prizes/redeem`

**Request:**
```json
{
  "prize_id": "uuid"
}
```

**Validation:**
1. Prize exists and is active
2. User has enough available scores
3. User is authenticated

**Process:**
1. Start database transaction
2. Check available scores again (prevent race conditions)
3. Create redemption record with status='pending' or 'approved' (depending on workflow)
4. Generate unique redemption code
5. Commit transaction
6. Send notification (email/in-app) with code

**Response:**
```json
{
  "success": true,
  "redemption": {
    "id": "uuid",
    "redemption_code": "BEER-ABC123",
    "prize_name": "Beer Voucher",
    "status": "approved",
    "redeemed_at": "2026-03-29T10:30:00Z"
  },
  "new_balance": {
    "available_scores": 90
  }
}
```

### 3. Admin Views Redemptions (Filament)

**Filament Resource:** `PrizeRedemptionResource`

**Table columns:**
- User name
- Prize name
- Score cost
- Redemption code
- Status badge
- Redeemed date
- Actions (View, Approve, Complete, Cancel)

**Filters:**
- Status (pending, approved, completed, cancelled)
- Date range
- User search
- Prize type

### 4. Admin Approves Redemption (if workflow enabled)

**Action:** `Tables\Actions\Action::make('approve')`

**Process:**
1. Update status to 'approved'
2. Set `approved_at` timestamp
3. Send notification to user

### 5. User Claims Prize (IRL)

**When user shows code at location:**
- Staff checks code in admin panel
- Mark as 'completed'
- Set `completed_at` timestamp

---

## Score Calculation Logic

### Available Scores Formula

```php
$totalEarned = Score::where('user_id', $userId)
    ->where('status', 1)
    ->sum('amount');

$totalSpent = PrizeRedemption::where('user_id', $userId)
    ->whereIn('status', ['pending', 'approved', 'completed']) // Not cancelled
    ->sum('score_cost');

$availableScores = $totalEarned - $totalSpent;
```

**Update `ScoreService::getAvailableScores()` to use this logic.**

---

## Models & Relationships

### PrizeRedemption Model

```php
class PrizeRedemption extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'prize_id',
        'prize_name',
        'prize_amount',
        'score_cost',
        'status',
        'redemption_code',
        'notes',
        'redeemed_at',
        'approved_at',
        'completed_at',
        'cancelled_at',
    ];

    protected $casts = [
        'prize_amount' => 'integer',
        'score_cost' => 'integer',
        'redeemed_at' => 'datetime',
        'approved_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function prize()
    {
        return $this->belongsTo(Prize::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    // Methods
    public function approve()
    {
        $this->update([
            'status' => 'approved',
            'approved_at' => now(),
        ]);
    }

    public function complete()
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
    }

    public function cancel()
    {
        $this->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);
    }

    public function generateCode(): string
    {
        return strtoupper(substr($this->prize_name, 0, 4) . '-' . substr(md5(uniqid()), 0, 6));
    }
}
```

### Update User Model

```php
public function prizeRedemptions()
{
    return $this->hasMany(PrizeRedemption::class);
}
```

### Update Prize Model

```php
public function redemptions()
{
    return $this->hasMany(PrizeRedemption::class);
}
```

---

## API Endpoints

### Public (Authenticated Users)

```
GET    /api/prizes                 - List active prizes with user balance
POST   /api/prizes/redeem          - Redeem a prize
GET    /api/prizes/redemptions     - User's redemption history
GET    /api/prizes/redemptions/{id} - Single redemption details
```

### Admin Only

```
GET    /admin/prize-redemptions            - Filament resource (view all)
POST   /admin/prize-redemptions/{id}/approve  - Approve redemption
POST   /admin/prize-redemptions/{id}/complete - Mark as completed
POST   /admin/prize-redemptions/{id}/cancel   - Cancel and refund
```

---

## UI/UX Considerations

### User-Facing Pages

**1. Prizes Catalog Page** (`/prizes`)
- Grid/list of available prizes
- Show prize image, name, description
- Display cost in score points
- Show "Redeem" button (disabled if insufficient scores)
- User's score balance at top

**2. Redemption Confirmation Modal**
- "You're about to redeem [Prize Name] for [Cost] points"
- "Your new balance will be [NewBalance] points"
- Confirm / Cancel buttons

**3. Success Page/Modal**
- "Redemption successful!"
- Display redemption code prominently
- Instructions on how to claim
- "View My Redemptions" button

**4. My Redemptions Page** (`/profile/redemptions`)
- List of user's past redemptions
- Show code, status, date
- Filter by status

### Admin Panel (Filament)

**PrizeRedemptionResource:**
- Table with all redemptions
- Bulk actions (approve multiple, etc.)
- Filters (status, date, user, prize)
- Export to CSV for reporting

---

## Implementation Steps

### Phase 1: Database & Models
1. ✅ Create `prize_redemptions` migration (with existence checks)
2. ✅ Create `PrizeRedemption` model with relationships
3. ✅ Update `User`, `Prize` models with relationships
4. ✅ Update `ScoreService::getAvailableScores()` to subtract spent scores

### Phase 2: Admin Panel (Filament)
5. ✅ Create `PrizeRedemptionResource` for Filament
6. ✅ Add table columns, filters, actions
7. ✅ Implement approve/complete/cancel actions
8. ✅ Add to navigation

### Phase 3: Backend API
9. ✅ Create `PrizeController` with redemption logic
10. ✅ Add validation and error handling
11. ✅ Implement transaction safety (prevent double-spending)
12. ✅ Add API routes

### Phase 4: Frontend (if applicable)
13. Create prizes catalog page
14. Create redemption flow (modal/pages)
15. Add user balance display
16. Create "My Redemptions" page

### Phase 5: Testing & Polish
17. Test race conditions (simultaneous redemptions)
18. Test insufficient balance scenarios
19. Add notifications (email/in-app)
20. Add logging for auditing

---

## Edge Cases & Considerations

### Security
- ✅ Prevent race conditions with database transactions
- ✅ Validate available scores at redemption time (not just client-side)
- ✅ Generate cryptographically secure redemption codes
- ✅ Rate limit redemption endpoint (prevent abuse)

### Business Logic
- ❓ **Approval workflow:** Auto-approve or require admin approval?
  - Suggestion: Auto-approve for now, add approval workflow later if needed
- ❓ **Expiry:** Should redemption codes expire after X days?
  - Suggestion: Add `expires_at` column, configurable per prize
- ❓ **Refunds:** Should users be able to cancel redemptions themselves?
  - Suggestion: Admin-only for now
- ❓ **Multiple redemptions:** Can user redeem same prize multiple times?
  - Suggestion: Yes, as long as they have scores

### Data Integrity
- Store snapshot of prize data at redemption time
- Never delete prize records that have redemptions (soft delete if needed)
- Log all status changes for audit trail

### Performance
- Index `user_id`, `prize_id`, `status` in redemptions table
- Cache user's available scores (invalidate on redemption/score change)
- Paginate redemption history

---

## Configuration Options

Consider adding to `config/prizes.php`:

```php
return [
    // Require admin approval before codes are active
    'require_approval' => false,

    // Days until redemption code expires (null = never)
    'code_expiry_days' => null,

    // Prefix for redemption codes
    'code_prefix' => 'BEER',

    // Allow users to cancel their own redemptions
    'allow_user_cancellation' => false,

    // Refund scores on cancellation
    'refund_on_cancel' => true,
];
```

---

## Future Enhancements

- **Notifications:** Email/SMS with redemption code
- **QR Codes:** Generate QR code for redemption code
- **Prize Inventory:** Track limited quantity prizes
- **Prize Categories:** Group prizes (Drinks, Food, Experiences)
- **Seasonal Prizes:** Auto-activate/deactivate based on dates
- **Redemption Stats:** Analytics dashboard for admins
- **Leaderboards:** Show top redeemers
- **Special Offers:** Bonus prizes, discounts, flash sales

---

## Questions to Resolve

1. **Approval workflow:** Auto-approve or require manual approval?
   - **Recommendation:** Auto-approve for MVP, add approval later if needed

2. **Code expiry:** Should codes expire?
   - **Recommendation:** No expiry for MVP, add later if needed

3. **Prize images:** Need to add image upload to prizes?
   - **Recommendation:** Yes, add `image` field to prizes table

4. **Email notifications:** Send confirmation emails?
   - **Recommendation:** Yes, use Laravel notifications

5. **Frontend framework:** Blade, React/Inertia, or API-only?
   - **Recommendation:** TBD based on existing app structure

---

## Success Metrics

- Number of redemptions per day/week/month
- Average scores earned before first redemption
- Most popular prizes
- Redemption completion rate (redeemed → completed)
- User engagement (repeat redemptions)

---

## Timeline Estimate

- **Phase 1 (Database & Models):** 2-3 hours
- **Phase 2 (Admin Panel):** 3-4 hours
- **Phase 3 (Backend API):** 4-5 hours
- **Phase 4 (Frontend):** 6-8 hours (if needed)
- **Phase 5 (Testing):** 2-3 hours

**Total:** ~17-23 hours

---

## Notes

- Follow CLAUDE.md guidelines (UUID primary keys, no foreign keys, idempotent migrations)
- All monetary/score transactions must be within database transactions
- Log all redemptions for audit trail
- Consider adding `deleted_at` (soft deletes) to prizes table to prevent breaking redemption history

---

**Status:** Ready for implementation approval
**Next Step:** Confirm approval workflow preference, then begin Phase 1

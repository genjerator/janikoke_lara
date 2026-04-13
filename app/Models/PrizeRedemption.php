<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function prize(): BelongsTo
    {
        return $this->belongsTo(Prize::class);
    }

    // Scopes
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', 'approved');
    }

    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', 'completed');
    }

    public function scopeCancelled(Builder $query): Builder
    {
        return $query->where('status', 'cancelled');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereIn('status', ['pending', 'approved', 'completed']);
    }

    // Status transition methods
    public function approve(): void
    {
        $this->update([
            'status' => 'approved',
            'approved_at' => now(),
        ]);
    }

    public function complete(): void
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
    }

    public function cancel(): void
    {
        $this->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);
    }

    public static function generateCode(string $prizeName): string
    {
        $prefix = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $prizeName), 0, 4));
        if (empty($prefix)) {
            $prefix = 'PRIZE';
        }
        return $prefix . '-' . strtoupper(substr(md5(uniqid((string)mt_rand(), true)), 0, 6));
    }
}

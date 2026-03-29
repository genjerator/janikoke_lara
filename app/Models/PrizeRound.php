<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrizeRound extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'prize_round';

    protected $fillable = [
        'prize_id',
        'round_id',
        'is_active',
        'custom_cost',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'custom_cost' => 'integer',
    ];

    /**
     * Get the prize for this prize-round entry.
     */
    public function prize(): BelongsTo
    {
        return $this->belongsTo(Prize::class);
    }

    /**
     * Get the round for this prize-round entry.
     */
    public function round(): BelongsTo
    {
        return $this->belongsTo(Round::class);
    }

    /**
     * Get the effective cost for this prize in this round.
     * Uses custom_cost if set, otherwise falls back to prize's default cost.
     */
    public function getEffectiveCost(): int
    {
        return $this->custom_cost ?? $this->prize->cost;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Prize extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'amount',
        'cost',
        'status',
        'name',
        'description',
        'content',
        'image',
    ];

    protected $casts = [
        'amount' => 'integer',
        'cost' => 'integer',
        'status' => 'integer',
    ];

    /**
     * Get the rounds this prize is available in.
     */
    public function rounds(): BelongsToMany
    {
        return $this->belongsToMany(Round::class, 'prize_round')
            ->withPivot('is_active', 'custom_cost')
            ->withTimestamps();
    }

    /**
     * Get all redemptions for this prize.
     */
    public function redemptions(): HasMany
    {
        return $this->hasMany(PrizeRedemption::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Prize extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'challenge_area_id',
        'round_id',
        'amount',
        'status',
        'name',
        'description',
        'content',
    ];

    protected $casts = [
        'amount' => 'integer',
        'status' => 'integer',
    ];

    /**
     * Get the user that owns the prize.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the challenge area associated with the prize.
     */
    public function challengeArea(): BelongsTo
    {
        return $this->belongsTo(ChallengeArea::class);
    }

    /**
     * Get the round associated with the prize.
     */
    public function round(): BelongsTo
    {
        return $this->belongsTo(Round::class);
    }
}

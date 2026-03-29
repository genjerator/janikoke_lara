<?php

namespace App\Models;

use App\Models\Scopes\isActiveScope;
use App\Models\HasIsActiveScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Round extends Model
{
    use HasFactory, HasIsActiveScope;
    protected string $name;
    protected string $description;
    protected \DateTimeImmutable $starts_at;
    protected \DateTimeImmutable $ends_at;
    protected $fillable = [
        'name',
        'description',
        'starts_at',
        'ends_at',
    ];

    /**
     * @return HasMany
     */
    public function challenges():HasMany
    {
        return $this->hasMany(Challenge::class);
    }

    public function scopeOnlyActive($query)
    {
        return $query->withGlobalScope('is_active', new IsActiveScope);
    }
    public function activeChallenges():HasMany
    {
        return $this->hasMany(Challenge::class)->active();
    }

    /**
     * Get the prizes available in this round.
     */
    public function prizes(): BelongsToMany
    {
        return $this->belongsToMany(Prize::class, 'prize_round')
            ->withPivot('is_active', 'custom_cost')
            ->withTimestamps();
    }
}

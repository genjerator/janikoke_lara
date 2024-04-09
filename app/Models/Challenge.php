<?php

namespace App\Models;

use App\Enums\ChallengeTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Challenge extends Model
{
    use HasFactory;
    protected string $name;
    protected string $description;
    protected $fillable = [
        'name',
        'description',
        'type'
    ];

    public function round()
    {
        return $this->belongsTo(Round::class);
    }
    public function areas()
    {
        return $this->belongsToMany(Area::class, 'challenge_area');
    }

    public function challengeAreas()
    {
        return $this->belongsToMany(ChallengeArea::class, 'challenge_area');
    }
    public function usersChallengeAreas(): HasManyThrough
    {
        return $this->hasManyThrough(UserChallengeArea::class, ChallengeArea::class);
    }

    protected $attributes = [
        'type' => ChallengeTypeEnum::TenEach->value
    ];

    // Define a mutator to cast the 'type' attribute to the ChallengeType enum
    public function setTypeAttribute($value)
    {
        $this->attributes['type'] = match ($value) {
            'TenEach' => ChallengeTypeEnum::TenEach->value,
            'Zigzag' => ChallengeTypeEnum::Zigzag->value,
            default => ChallengeTypeEnum::TenEach->value,
        };
    }

}

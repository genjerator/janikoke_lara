<?php

namespace App\Models;

use App\Enums\ChallengeTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

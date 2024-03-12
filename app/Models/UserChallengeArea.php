<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserChallengeArea extends Model
{
    use HasFactory;
    protected $table = 'user_challenge_area';
    protected $fillable = [
        'user_id',
        'challenge_area_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function challengeArea()
    {
        return $this->belongsTo(ChallengeArea::class);
    }
}

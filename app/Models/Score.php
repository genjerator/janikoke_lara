<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Score extends Model
{
    use HasFactory;

    // Define the table name if it's different from the model's plural form
    protected $table = 'scores';

    // Specify the fields that are mass assignable
    protected $fillable = [
        'user_id',
        'challenge_area_id',
        'round_id',
        'amount',
        'status',
        'name',
        'description',
    ];

    // Define the relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function challengeArea()
    {
        return $this->belongsTo(ChallengeArea::class);
    }

    public function round()
    {
        return $this->belongsTo(Round::class);
    }
}

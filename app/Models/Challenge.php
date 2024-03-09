<?php

namespace App\Models;

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
    ];

    public function round()
    {
        return $this->belongsTo(Round::class);
    }
    public function areas()
    {
        return $this->belongsToMany(Area::class, 'challenge_area');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'day_of_birth',
        'day_of_die',
    ];

    protected $casts = [
        'day_of_birth' => 'date',
        'day_of_die' => 'date',
    ];
    public function area()
    {
        return $this->belongsTo(Area::class);
    }
}

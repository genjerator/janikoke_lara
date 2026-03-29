<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
    ];

    protected $casts = [
        'amount' => 'integer',
        'cost' => 'integer',
        'status' => 'integer',
    ];
}

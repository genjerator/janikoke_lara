<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use MatanYadaev\EloquentSpatial\Objects\Point;
use MatanYadaev\EloquentSpatial\Objects\Polygon;

class Area extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'description',
        'location',
        'area',
    ];

    protected $casts = [
        'location' => Point::class,
        'area' => Polygon::class,
    ];
    public function challenges()
    {
        return $this->belongsToMany(Challenge::class, 'challenge_area');
    }
}

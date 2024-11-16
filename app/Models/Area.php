<?php

namespace App\Models;

use App\Models\HasIsActiveScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use MatanYadaev\EloquentSpatial\Objects\Point;
use MatanYadaev\EloquentSpatial\Objects\Polygon;

class Area extends Model
{
    use HasFactory, HasIsActiveScope;

    protected $fillable = [
        'name',
        'description',
        'location',
        'area'
    ];
    //protected $appends= ['json_polygon'];
    protected $casts = [
        'location' => Point::class,
        'area' => Polygon::class,
    ];

    public function getJsonPolygonAttribute()
    {
        if (!$this->area || !$this->area instanceof Polygon) {
            return json_encode([]);
        }

        $lineString = $this->area->getGeometries()[0];

        $points = $lineString->getGeometries();
        $coordinates = $points->map(fn ($point) => [
            'lat' => $point->latitude,
            'lng' => $point->longitude,
        ])->toArray();

        return json_encode($coordinates);
    }

    public function challenges()
    {
        return $this->belongsToMany(Challenge::class, 'challenge_area');
    }

    public function usersChallengeAreas(): HasManyThrough
    {
        return $this->hasManyThrough(UserChallengeArea::class, ChallengeArea::class);
    }

}

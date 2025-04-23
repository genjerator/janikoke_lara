<?php

namespace App\Models;

use App\Models\HasIsActiveScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use MatanYadaev\EloquentSpatial\Objects\LineString;
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

    public function getJsonPolygonAttribute(): string
    {
        if (!$this->area || !$this->area instanceof Polygon) {
            return json_encode([]);
        }

        $lineString = $this->area->getGeometries()[0];

        $points = $lineString->getGeometries();
        $coordinates = $points->map(fn($point) => [
            'lat' => $point->latitude,
            'lng' => $point->longitude,
        ])->toArray();

        return json_encode($coordinates);
    }

    /**
     * Get polygon coordinates as [lat, lng] array (for Leaflet.js).
     */
    public function getPolygonCoordinatesForLeaflet(): array
    {
        if (!$this->area || !$this->area instanceof Polygon) {
            return json_encode([]);
        }

        $lineString = $this->area->getGeometries()[0];

        $points = $lineString->getGeometries();
        $coordinates = $points->map(fn($point) => [
            'latitude' => $point->latitude,
            'longitude' => $point->longitude,
        ])->toArray();

        return $coordinates;
    }

    public function setJsonPolygonAttribute(string $json): void
    {
        $jsonDecode = json_decode($json, true);
        $this->area->setGeometries($jsonDecode);
    }

    public static function createPolygonFromData(array $data): array
    {
        $json = json_decode($data['json_polygon'], true);
        if ($json[0] !== $json[count($json) - 1]) {
            array_push($json, $json[0]);
        }
        $points = collect($json)->map(fn($coordinates) => new Point($coordinates['lat'], $coordinates['lng']));

        $lineString = new LineString($points);
        $polygon = new Polygon([$lineString]);
        $data['area'] = $polygon;
        return $data;
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

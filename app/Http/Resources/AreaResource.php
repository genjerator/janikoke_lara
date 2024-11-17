<?php

namespace App\Http\Resources;

use App\Http\Services\InsideAreaService;
use App\Models\Area;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\App;
use MatanYadaev\EloquentSpatial\Objects\Geometry;

/**
 * @property Area $resource
 */
class AreaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'description' => $this->resource->description,
            'polygons' => array_map(
                function ($coordinate) {
                    return [
                        'longitude' => $coordinate[0],
                        'latitude' => $coordinate[1]
                    ];
                }
            ,$this->resource->area->getCoordinates()[0]),
        ];
    }
}

<?php

namespace App\Http\Resources;

use App\Models\Area;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
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
        //dd($this->resource->area->getCoordinates());
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'description' => $this->resource->description,
            'polygons' => array_map(
                function ($coordinate) {
                    return [
                        'latitude' => $coordinate[0],
                        'longitude' => $coordinate[1]
                    ];
                }
            ,$this->resource->area->getCoordinates()[0]),
        ];
    }
}

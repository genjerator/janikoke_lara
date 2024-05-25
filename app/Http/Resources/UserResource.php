<?php

namespace App\Http\Resources;

use App\Http\Services\InsideAreaService;
use App\Models\Area;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\App;
use MatanYadaev\EloquentSpatial\Objects\Geometry;

/**
 * @property User $resource
 */
class UserResource extends JsonResource
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
            'email' => $this->resource->email,
            'token' => $this->resource->createToken("API TOKEN")->plainTextToken,
        ];
    }
}

<?php

namespace App\Http\Resources;

use App\Models\Challenge;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


/**
 * @property Challenge $resource
 */
class ChallengeResource extends JsonResource
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
            'areas' => AreaResource::collection($this->resource->areas),
        ];
    }
}

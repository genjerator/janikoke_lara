<?php

namespace App\Http\Resources;

use App\Models\Score;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property Score $resource
 */
class ScoreResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "challenge_id" => $this->resource->challengeArea->challenge->id,
            "area_id" => $this->resource->challengeArea->area->id,
            "user" => $this->resource->user->name,
            "challenge_name" => $this->resource->challengeArea->challenge->name,
            "challenge_type" => $this->resource->challengeArea->challenge->type,
            "challenge_description" => $this->resource->challengeArea->challenge->description,
            "area_name" => $this->resource->challengeArea->area->id,
            "time" => strtotime($this->resource->created_at),
            "points" => $this->resource->amount
        ];
    }
}

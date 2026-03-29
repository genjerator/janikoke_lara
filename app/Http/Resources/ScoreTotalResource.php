<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ScoreTotalResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'success' => true,
            'round_id' => $this->resource['round_id'],
            'user_id' => $this->resource['user_id'],
            'total' => $this->resource['total'],
        ];
    }
}

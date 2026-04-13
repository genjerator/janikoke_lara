<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class PrizeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $cost = $this->whenPivotLoaded('prize_round', function () {
            return $this->pivot->custom_cost ?? $this->cost;
        }, $this->cost);

        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'amount' => $this->amount,
            'cost' => $cost,
            'content' => $this->content,
            'image_url' => $this->image
                ? Storage::disk('public')->url($this->image)
                : null,
            'can_afford' => $this->when(
                auth()->check(),
                fn () => ($this->additional['available_scores'] ?? 0) >= $cost
            ),
        ];
    }
}

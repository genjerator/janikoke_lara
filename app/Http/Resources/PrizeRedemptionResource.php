<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PrizeRedemptionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'prize_id' => $this->prize_id,
            'prize_name' => $this->prize_name,
            'prize_amount' => $this->prize_amount,
            'score_cost' => $this->score_cost,
            'status' => $this->status,
            'redemption_code' => $this->redemption_code,
            'notes' => $this->notes,
            'redeemed_at' => $this->redeemed_at?->toIso8601String(),
            'approved_at' => $this->approved_at?->toIso8601String(),
            'completed_at' => $this->completed_at?->toIso8601String(),
            'cancelled_at' => $this->cancelled_at?->toIso8601String(),
        ];
    }
}

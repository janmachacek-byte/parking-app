<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ParkingSpotResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'spot' => $this->spot,
            'updatedAt' => $this->updated_at?->toISOString(),
        ];
    }
}
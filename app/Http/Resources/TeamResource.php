<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TeamResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'logo_url' => $this->logo_url,
            'region' => $this->region,
            'rank' => $this->rank,
            'earnings' => (float) $this->earnings,
            'game' => GameResource::make($this->whenLoaded('game')),
            'likes_count' => $this->when($this->liked_by_users_count !== null, $this->liked_by_users_count),
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}

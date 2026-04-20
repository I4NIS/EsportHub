<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlayerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'pseudo' => $this->pseudo,
            'real_name' => $this->real_name,
            'nationality' => $this->nationality,
            'photo_url' => $this->photo_url,
            'game' => GameResource::make($this->whenLoaded('game')),
            'current_team' => TeamResource::make($this->whenLoaded('currentTeam')),
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}

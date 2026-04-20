<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MatchResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'event' => EventResource::make($this->whenLoaded('event')),
            'team1' => TeamResource::make($this->whenLoaded('team1')),
            'team2' => TeamResource::make($this->whenLoaded('team2')),
            'score_team1' => $this->score_team1,
            'score_team2' => $this->score_team2,
            'status' => $this->status,
            'scheduled_at' => $this->scheduled_at?->toISOString(),
            'maps' => MatchMapResource::collection($this->whenLoaded('maps')),
        ];
    }
}

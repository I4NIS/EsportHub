<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlayerStatResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'player' => PlayerResource::make($this->whenLoaded('player')),
            'match' => MatchResource::make($this->whenLoaded('match')),
            'match_map' => MatchMapResource::make($this->whenLoaded('matchMap')),
            'team' => TeamResource::make($this->whenLoaded('team')),
            'region' => $this->region,
            'rating' => $this->rating,
            'acs' => $this->acs,
            'kd_ratio' => $this->kd_ratio,
            'kast' => $this->kast,
            'adr' => $this->adr,
            'kpr' => $this->kpr,
            'headshot_pct' => $this->headshot_pct,
            'clutch_pct' => $this->clutch_pct,
        ];
    }
}

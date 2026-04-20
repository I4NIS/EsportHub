<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MatchMapResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'map_name' => $this->map_name,
            'map_number' => $this->map_number,
            'team1_round' => $this->team1_round,
            'team2_round' => $this->team2_round,
            'status' => $this->status,
        ];
    }
}

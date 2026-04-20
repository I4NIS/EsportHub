<?php

namespace Database\Factories;

use App\Models\GameMatch;
use App\Models\MatchMap;
use App\Models\Player;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

class PlayerStatFactory extends Factory
{
    public function definition(): array
    {
        $regions = ['NA', 'EU', 'APAC', 'SA'];
        return [
            'player_id' => Player::factory(),
            'match_id' => GameMatch::factory(),
            'match_map_id' => null,
            'team_id' => Team::factory(),
            'region' => fake()->randomElement($regions),
            'rating' => fake()->randomFloat(2, 0.5, 2.0),
            'acs' => fake()->randomFloat(1, 100, 350),
            'kd_ratio' => fake()->randomFloat(2, 0.5, 3.0),
            'kast' => fake()->randomFloat(2, 40, 95),
            'adr' => fake()->randomFloat(1, 60, 250),
            'kpr' => fake()->randomFloat(2, 0.3, 1.5),
            'headshot_pct' => fake()->randomFloat(2, 10, 60),
            'clutch_pct' => fake()->randomFloat(2, 0, 50),
        ];
    }
}

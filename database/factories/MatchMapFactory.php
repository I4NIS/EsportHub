<?php

namespace Database\Factories;

use App\Models\GameMatch;
use Illuminate\Database\Eloquent\Factories\Factory;

class MatchMapFactory extends Factory
{
    public function definition(): array
    {
        $valorantMaps = ['Bind', 'Haven', 'Split', 'Ascent', 'Icebox', 'Breeze', 'Fracture', 'Pearl', 'Lotus', 'Sunset'];
        $cs2Maps = ['de_dust2', 'de_mirage', 'de_inferno', 'de_nuke', 'de_overpass', 'de_vertigo', 'de_ancient', 'de_anubis'];
        return [
            'match_id' => GameMatch::factory(),
            'map_name' => fake()->randomElement(array_merge($valorantMaps, $cs2Maps)),
            'map_number' => fake()->numberBetween(1, 5),
            'team1_round' => fake()->numberBetween(0, 13),
            'team2_round' => fake()->numberBetween(0, 13),
            'status' => fake()->randomElement(['upcoming', 'live', 'completed']),
        ];
    }
}

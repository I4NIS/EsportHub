<?php

namespace Database\Factories;

use App\Models\Game;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

class PlayerFactory extends Factory
{
    public function definition(): array
    {
        $nationalities = ['FR', 'US', 'BR', 'KR', 'DE', 'GB', 'SE', 'DK', 'PL', 'PT'];
        return [
            'game_id' => Game::factory(),
            'current_team_id' => null,
            'pseudo' => fake()->unique()->userName(),
            'real_name' => fake()->name(),
            'nationality' => fake()->randomElement($nationalities),
            'photo_url' => 'https://placehold.co/200x200?text=Player',
        ];
    }
}

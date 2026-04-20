<?php

namespace Database\Factories;

use App\Models\Game;
use Illuminate\Database\Eloquent\Factories\Factory;

class TeamFactory extends Factory
{
    public function definition(): array
    {
        $regions = ['NA', 'EU', 'APAC', 'SA', 'ME'];
        return [
            'game_id' => Game::factory(),
            'name' => fake()->unique()->company(),
            'logo_url' => 'https://placehold.co/200x200?text=Team',
            'region' => fake()->randomElement($regions),
            'rank' => fake()->numberBetween(1, 100),
            'earnings' => fake()->randomFloat(2, 0, 5000000),
        ];
    }
}

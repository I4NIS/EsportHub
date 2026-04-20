<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

class GameMatchFactory extends Factory
{
    public function definition(): array
    {
        return [
            'event_id' => Event::factory(),
            'team1_id' => Team::factory(),
            'team2_id' => Team::factory(),
            'score_team1' => fake()->numberBetween(0, 3),
            'score_team2' => fake()->numberBetween(0, 3),
            'status' => fake()->randomElement(['upcoming', 'live', 'completed']),
            'scheduled_at' => fake()->dateTimeBetween('-1 month', '+1 month'),
        ];
    }
}

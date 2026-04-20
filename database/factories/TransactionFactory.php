<?php

namespace Database\Factories;

use App\Models\Player;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransactionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'player_id' => Player::factory(),
            'team_id' => Team::factory(),
            'type' => fake()->randomElement(['join', 'leave']),
            'transaction_date' => fake()->date(),
            'description' => fake()->optional()->sentence(),
        ];
    }
}

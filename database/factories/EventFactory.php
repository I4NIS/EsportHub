<?php

namespace Database\Factories;

use App\Models\Game;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventFactory extends Factory
{
    public function definition(): array
    {
        $start = fake()->dateTimeBetween('-6 months', '+3 months');
        $endDate = (clone $start)->modify('+3 months');
        $end = fake()->dateTimeBetween($start, $endDate);
        $statuses = ['upcoming', 'ongoing', 'completed'];
        return [
            'game_id' => Game::factory(),
            'name' => fake()->words(3, true) . ' Championship',
            'logo_url' => 'https://placehold.co/200x200?text=Event',
            'prize_pool' => '$' . number_format(fake()->randomElement([50000, 100000, 250000, 1000000])),
            'start_date' => $start,
            'end_date' => $end,
            'status' => fake()->randomElement($statuses),
        ];
    }
}

<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class GameFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->randomElement([
            'Valorant', 'Counter-Strike 2', 'Apex Legends', 'Overwatch 2', 'Rainbow Six Siege',
        ]);
        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'logo_url' => 'https://placehold.co/200x200?text=' . urlencode($name),
        ];
    }
}

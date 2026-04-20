<?php

use App\Models\Game;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('GET /api/games', function () {
    it('returns list of games', function () {
        Game::factory()->count(3)->create();

        $response = $this->getJson('/api/games');

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonCount(3, 'data');
    });

    it('returns empty list when no games', function () {
        $this->getJson('/api/games')
            ->assertStatus(200)
            ->assertJson(['success' => true, 'data' => []]);
    });

    it('returns correct game structure', function () {
        Game::factory()->create(['name' => 'Valorant', 'slug' => 'valorant']);

        $this->getJson('/api/games')
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [['id', 'name', 'slug', 'logo_url']],
            ]);
    });
});

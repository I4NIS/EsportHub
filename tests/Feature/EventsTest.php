<?php

use App\Models\Event;
use App\Models\Game;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('GET /api/events', function () {
    it('returns list of events with game', function () {
        Event::factory()->count(3)->create();

        $response = $this->getJson('/api/events');

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'data' => [['id', 'name', 'logo_url', 'prize_pool', 'start_date', 'end_date', 'status', 'game']],
            ]);
    });

    it('returns empty list when no events', function () {
        $this->getJson('/api/events')
            ->assertStatus(200)
            ->assertJson(['data' => []]);
    });

    it('includes game data in response', function () {
        $game = Game::factory()->create(['name' => 'Valorant']);
        Event::factory()->create(['game_id' => $game->id, 'name' => 'Masters Tokyo']);

        $data = $this->getJson('/api/events')->json('data');
        expect($data[0]['game']['name'])->toBe('Valorant');
    });
});

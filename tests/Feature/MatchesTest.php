<?php

use App\Models\Event;
use App\Models\Game;
use App\Models\GameMatch;
use App\Models\MatchMap;
use App\Models\Player;
use App\Models\PlayerStat;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('GET /api/matches', function () {
    it('returns paginated list of matches', function () {
        $game = Game::factory()->create();
        $t1 = Team::factory()->create(['game_id' => $game->id]);
        $t2 = Team::factory()->create(['game_id' => $game->id]);
        GameMatch::factory()->count(3)->create(['event_id' => null, 'team1_id' => $t1->id, 'team2_id' => $t2->id]);

        $this->getJson('/api/matches')
            ->assertStatus(200)
            ->assertJsonStructure(['success', 'message', 'data']);
    });

    it('filters by status', function () {
        $game = Game::factory()->create();
        $t1 = Team::factory()->create(['game_id' => $game->id]);
        $t2 = Team::factory()->create(['game_id' => $game->id]);
        GameMatch::factory()->create(['event_id' => null, 'team1_id' => $t1->id, 'team2_id' => $t2->id, 'status' => 'live']);
        GameMatch::factory()->create(['event_id' => null, 'team1_id' => $t1->id, 'team2_id' => $t2->id, 'status' => 'completed']);

        $data = $this->getJson('/api/matches?status=live')->json('data');
        expect(count($data))->toBe(1);
    });

    it('returns correct match structure', function () {
        $game = Game::factory()->create();
        $t1 = Team::factory()->create(['game_id' => $game->id]);
        $t2 = Team::factory()->create(['game_id' => $game->id]);
        GameMatch::factory()->create(['event_id' => null, 'team1_id' => $t1->id, 'team2_id' => $t2->id]);

        $this->getJson('/api/matches')
            ->assertJsonStructure([
                'data' => [['id', 'status', 'score_team1', 'score_team2', 'scheduled_at', 'team1', 'team2']],
            ]);
    });
});

describe('GET /api/matches/live', function () {
    it('returns only live matches', function () {
        $game = Game::factory()->create();
        $t1 = Team::factory()->create(['game_id' => $game->id]);
        $t2 = Team::factory()->create(['game_id' => $game->id]);
        GameMatch::factory()->create(['event_id' => null, 'team1_id' => $t1->id, 'team2_id' => $t2->id, 'status' => 'live']);
        GameMatch::factory()->create(['event_id' => null, 'team1_id' => $t1->id, 'team2_id' => $t2->id, 'status' => 'upcoming']);
        GameMatch::factory()->create(['event_id' => null, 'team1_id' => $t1->id, 'team2_id' => $t2->id, 'status' => 'completed']);

        $data = $this->getJson('/api/matches/live')->json('data');
        expect(count($data))->toBe(1);
    });
});

describe('GET /api/matches/{id}', function () {
    it('returns match with event, teams and maps', function () {
        $game = Game::factory()->create();
        $t1 = Team::factory()->create(['game_id' => $game->id]);
        $t2 = Team::factory()->create(['game_id' => $game->id]);
        $match = GameMatch::factory()->create(['event_id' => null, 'team1_id' => $t1->id, 'team2_id' => $t2->id]);
        MatchMap::factory()->create(['match_id' => $match->id]);

        $this->getJson("/api/matches/{$match->id}")
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => ['id', 'status', 'team1', 'team2', 'maps'],
            ]);
    });

    it('returns 404 for unknown match', function () {
        $this->getJson('/api/matches/00000000-0000-0000-0000-000000000000')
            ->assertStatus(404);
    });
});

describe('GET /api/matches/{id}/stats', function () {
    it('returns player stats for match', function () {
        $game = Game::factory()->create();
        $t1 = Team::factory()->create(['game_id' => $game->id]);
        $t2 = Team::factory()->create(['game_id' => $game->id]);
        $match = GameMatch::factory()->create(['event_id' => null, 'team1_id' => $t1->id, 'team2_id' => $t2->id]);
        $player = Player::factory()->create(['game_id' => $game->id]);
        PlayerStat::factory()->create([
            'match_id' => $match->id,
            'player_id' => $player->id,
            'team_id' => $t1->id,
        ]);

        $this->getJson("/api/matches/{$match->id}/stats")
            ->assertStatus(200)
            ->assertJsonCount(1, 'data');
    });
});

<?php

use App\Models\Game;
use App\Models\GameMatch;
use App\Models\Player;
use App\Models\Team;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('GET /api/teams', function () {
    it('returns paginated list of teams', function () {
        Team::factory()->count(5)->create();

        $this->getJson('/api/teams')
            ->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonStructure(['success', 'message', 'data']);
    });

    it('filters by region', function () {
        $game = Game::factory()->create();
        Team::factory()->create(['game_id' => $game->id, 'region' => 'EU']);
        Team::factory()->create(['game_id' => $game->id, 'region' => 'NA']);

        $data = $this->getJson('/api/teams?region=EU')->json('data');
        expect(count($data))->toBe(1);
    });

    it('filters by game slug', function () {
        $game = Game::factory()->create(['slug' => 'valorant']);
        $otherGame = Game::factory()->create(['slug' => 'cs2']);
        Team::factory()->create(['game_id' => $game->id]);
        Team::factory()->create(['game_id' => $otherGame->id]);

        $data = $this->getJson('/api/teams?game=valorant')->json('data');
        expect(count($data))->toBe(1);
    });
});

describe('GET /api/teams/search', function () {
    it('returns teams matching query', function () {
        Team::factory()->create(['name' => 'Team Liquid']);
        Team::factory()->create(['name' => 'Fnatic']);

        $data = $this->getJson('/api/teams/search?q=liquid')->json('data');
        expect(count($data))->toBe(1);
        expect($data[0]['name'])->toBe('Team Liquid');
    });

    it('is case insensitive', function () {
        Team::factory()->create(['name' => 'Sentinels']);

        $data = $this->getJson('/api/teams/search?q=SENTIN')->json('data');
        expect(count($data))->toBe(1);
    });

    it('returns empty when no match', function () {
        Team::factory()->create(['name' => 'Fnatic']);

        $data = $this->getJson('/api/teams/search?q=xxxxxxx')->json('data');
        expect(count($data))->toBe(0);
    });
});

describe('GET /api/teams/{id}', function () {
    it('returns team details with game and players', function () {
        $team = Team::factory()->create();

        $this->getJson("/api/teams/{$team->id}")
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => ['id', 'name', 'region', 'game', 'likes_count'],
            ]);
    });

    it('returns 404 for unknown team', function () {
        $this->getJson('/api/teams/00000000-0000-0000-0000-000000000000')
            ->assertStatus(404);
    });
});

describe('GET /api/teams/{id}/matches/live', function () {
    it('returns live matches for team', function () {
        $game = Game::factory()->create();
        $team = Team::factory()->create(['game_id' => $game->id]);
        $other = Team::factory()->create(['game_id' => $game->id]);

        GameMatch::factory()->create([
            'event_id' => null,
            'team1_id' => $team->id,
            'team2_id' => $other->id,
            'status' => 'live',
        ]);
        GameMatch::factory()->create([
            'event_id' => null,
            'team1_id' => $team->id,
            'team2_id' => $other->id,
            'status' => 'completed',
        ]);

        $data = $this->getJson("/api/teams/{$team->id}/matches/live")->json('data');
        expect(count($data))->toBe(1);
    });
});

describe('GET /api/teams/{id}/matches', function () {
    it('returns paginated matches for team', function () {
        $game = Game::factory()->create();
        $team = Team::factory()->create(['game_id' => $game->id]);
        $other = Team::factory()->create(['game_id' => $game->id]);

        GameMatch::factory()->count(3)->create([
            'event_id' => null,
            'team1_id' => $team->id,
            'team2_id' => $other->id,
        ]);

        $this->getJson("/api/teams/{$team->id}/matches")
            ->assertStatus(200)
            ->assertJsonStructure(['success', 'message', 'data']);
    });
});

describe('GET /api/teams/{id}/players', function () {
    it('returns players for team', function () {
        $game = Game::factory()->create();
        $team = Team::factory()->create(['game_id' => $game->id]);
        Player::factory()->count(3)->create(['game_id' => $game->id, 'current_team_id' => $team->id]);

        $data = $this->getJson("/api/teams/{$team->id}/players")->json('data');
        expect(count($data))->toBe(3);
    });
});

describe('GET /api/teams/{id}/transactions', function () {
    it('returns transactions for team', function () {
        $game = Game::factory()->create();
        $team = Team::factory()->create(['game_id' => $game->id]);
        $player = Player::factory()->create(['game_id' => $game->id]);
        Transaction::factory()->count(2)->create([
            'team_id' => $team->id,
            'player_id' => $player->id,
        ]);

        $data = $this->getJson("/api/teams/{$team->id}/transactions")->json('data');
        expect(count($data))->toBe(2);
    });
});

describe('GET /api/rankings', function () {
    it('returns teams with rank ordered ascending', function () {
        $game = Game::factory()->create();
        Team::factory()->create(['game_id' => $game->id, 'rank' => 3]);
        Team::factory()->create(['game_id' => $game->id, 'rank' => 1]);
        Team::factory()->create(['game_id' => $game->id, 'rank' => null]);

        $data = $this->getJson('/api/rankings')->json('data');
        expect(count($data))->toBe(2);
        expect($data[0]['rank'])->toBe(1);
    });
});

describe('POST /api/teams/{id}/like', function () {
    it('likes a team', function () {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;
        $team = Team::factory()->create();

        $this->withToken($token)
            ->postJson("/api/teams/{$team->id}/like")
            ->assertStatus(200);

        $this->assertDatabaseHas('user_team_likes', ['user_id' => $user->id, 'team_id' => $team->id]);
    });

    it('fails to like already liked team', function () {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;
        $team = Team::factory()->create();
        $user->likedTeams()->attach($team->id, ['liked_at' => now()]);

        $this->withToken($token)
            ->postJson("/api/teams/{$team->id}/like")
            ->assertStatus(400);
    });

    it('requires authentication', function () {
        $team = Team::factory()->create();
        $this->postJson("/api/teams/{$team->id}/like")->assertStatus(401);
    });
});

describe('DELETE /api/teams/{id}/like', function () {
    it('unlikes a team', function () {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;
        $team = Team::factory()->create();
        $user->likedTeams()->attach($team->id, ['liked_at' => now()]);

        $this->withToken($token)
            ->deleteJson("/api/teams/{$team->id}/like")
            ->assertStatus(200);

        $this->assertDatabaseMissing('user_team_likes', ['user_id' => $user->id, 'team_id' => $team->id]);
    });
});

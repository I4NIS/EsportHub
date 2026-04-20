<?php

use App\Models\Event;
use App\Models\Game;
use App\Models\GameMatch;
use App\Models\Player;
use App\Models\PlayerStat;
use App\Models\Team;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('GET /api/players', function () {
    it('returns paginated list of players', function () {
        Player::factory()->count(5)->create();

        $this->getJson('/api/players')
            ->assertStatus(200)
            ->assertJsonStructure(['success', 'message', 'data']);
    });

    it('filters by game slug', function () {
        $val = Game::factory()->create(['slug' => 'valorant']);
        $cs2 = Game::factory()->create(['slug' => 'cs2']);
        Player::factory()->create(['game_id' => $val->id]);
        Player::factory()->create(['game_id' => $cs2->id]);

        $data = $this->getJson('/api/players?game=valorant')->json('data');
        expect(count($data))->toBe(1);
    });

    it('filters by region via current team', function () {
        $game = Game::factory()->create();
        $euTeam = Team::factory()->create(['game_id' => $game->id, 'region' => 'EU']);
        $naTeam = Team::factory()->create(['game_id' => $game->id, 'region' => 'NA']);
        Player::factory()->create(['game_id' => $game->id, 'current_team_id' => $euTeam->id]);
        Player::factory()->create(['game_id' => $game->id, 'current_team_id' => $naTeam->id]);

        $data = $this->getJson('/api/players?region=EU')->json('data');
        expect(count($data))->toBe(1);
    });

    it('returns correct player structure', function () {
        Player::factory()->create();

        $this->getJson('/api/players')
            ->assertJsonStructure([
                'data' => [['id', 'pseudo', 'real_name', 'nationality', 'photo_url', 'game']],
            ]);
    });
});

describe('GET /api/players/search', function () {
    it('searches by pseudo case insensitively', function () {
        Player::factory()->create(['pseudo' => 'TenZ']);
        Player::factory()->create(['pseudo' => 'aspas']);

        $data = $this->getJson('/api/players/search?q=tenz')->json('data');
        expect(count($data))->toBe(1);
        expect($data[0]['pseudo'])->toBe('TenZ');
    });

    it('searches by real name', function () {
        Player::factory()->create(['real_name' => 'Tyson Ngo', 'pseudo' => 'TenZ']);
        Player::factory()->create(['real_name' => 'Felipe Azambuja', 'pseudo' => 'aspas']);

        $data = $this->getJson('/api/players/search?q=Tyson')->json('data');
        expect(count($data))->toBe(1);
    });

    it('returns empty when no match', function () {
        Player::factory()->create(['pseudo' => 'TenZ']);

        $data = $this->getJson('/api/players/search?q=zzzzz')->json('data');
        expect(count($data))->toBe(0);
    });
});

describe('GET /api/players/{id}', function () {
    it('returns player details', function () {
        $player = Player::factory()->create();

        $this->getJson("/api/players/{$player->id}")
            ->assertStatus(200)
            ->assertJsonStructure(['data' => ['id', 'pseudo', 'game']]);
    });

    it('returns 404 for unknown player', function () {
        $this->getJson('/api/players/00000000-0000-0000-0000-000000000000')
            ->assertStatus(404);
    });
});

describe('GET /api/players/{id}/stats', function () {
    it('returns stats for player', function () {
        $game = Game::factory()->create();
        $t1 = Team::factory()->create(['game_id' => $game->id]);
        $t2 = Team::factory()->create(['game_id' => $game->id]);
        $player = Player::factory()->create(['game_id' => $game->id]);
        $match = GameMatch::factory()->create(['team1_id' => $t1->id, 'team2_id' => $t2->id]);
        PlayerStat::factory()->count(2)->create([
            'player_id' => $player->id,
            'match_id' => $match->id,
            'team_id' => $t1->id,
        ]);

        $this->getJson("/api/players/{$player->id}/stats")
            ->assertStatus(200)
            ->assertJsonCount(2, 'data');
    });
});

describe('GET /api/players/{id}/teams', function () {
    it('returns team history via transactions', function () {
        $game = Game::factory()->create();
        $player = Player::factory()->create(['game_id' => $game->id]);
        $team = Team::factory()->create(['game_id' => $game->id]);
        Transaction::factory()->count(2)->create([
            'player_id' => $player->id,
            'team_id' => $team->id,
        ]);

        $this->getJson("/api/players/{$player->id}/teams")
            ->assertStatus(200)
            ->assertJsonCount(2, 'data');
    });
});

describe('GET /api/players/{id}/events', function () {
    it('returns events the player participated in', function () {
        $game = Game::factory()->create();
        $t1 = Team::factory()->create(['game_id' => $game->id]);
        $t2 = Team::factory()->create(['game_id' => $game->id]);
        $player = Player::factory()->create(['game_id' => $game->id]);
        $event = Event::factory()->create(['game_id' => $game->id]);
        $match = GameMatch::factory()->create([
            'event_id' => $event->id,
            'team1_id' => $t1->id,
            'team2_id' => $t2->id,
        ]);
        PlayerStat::factory()->create([
            'player_id' => $player->id,
            'match_id' => $match->id,
            'team_id' => $t1->id,
        ]);

        $data = $this->getJson("/api/players/{$player->id}/events")->json('data');
        expect(count($data))->toBe(1);
        expect($data[0]['id'])->toBe($event->id);
    });
});

describe('GET /api/players/{id}/matches', function () {
    it('returns matches the player participated in', function () {
        $game = Game::factory()->create();
        $t1 = Team::factory()->create(['game_id' => $game->id]);
        $t2 = Team::factory()->create(['game_id' => $game->id]);
        $player = Player::factory()->create(['game_id' => $game->id]);
        $match = GameMatch::factory()->create(['event_id' => null, 'team1_id' => $t1->id, 'team2_id' => $t2->id]);
        PlayerStat::factory()->create([
            'player_id' => $player->id,
            'match_id' => $match->id,
            'team_id' => $t1->id,
        ]);

        $this->getJson("/api/players/{$player->id}/matches")
            ->assertStatus(200)
            ->assertJsonCount(1, 'data');
    });
});

describe('POST /api/players/{id}/follow', function () {
    it('follows a player', function () {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;
        $player = Player::factory()->create();

        $this->withToken($token)
            ->postJson("/api/players/{$player->id}/follow")
            ->assertStatus(200);

        $this->assertDatabaseHas('user_player_follows', [
            'user_id' => $user->id,
            'player_id' => $player->id,
        ]);
    });

    it('fails to follow already followed player', function () {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;
        $player = Player::factory()->create();
        $user->followedPlayers()->attach($player->id, ['followed_at' => now()]);

        $this->withToken($token)
            ->postJson("/api/players/{$player->id}/follow")
            ->assertStatus(400);
    });

    it('requires authentication', function () {
        $player = Player::factory()->create();
        $this->postJson("/api/players/{$player->id}/follow")->assertStatus(401);
    });
});

describe('DELETE /api/players/{id}/follow', function () {
    it('unfollows a player', function () {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;
        $player = Player::factory()->create();
        $user->followedPlayers()->attach($player->id, ['followed_at' => now()]);

        $this->withToken($token)
            ->deleteJson("/api/players/{$player->id}/follow")
            ->assertStatus(200);

        $this->assertDatabaseMissing('user_player_follows', [
            'user_id' => $user->id,
            'player_id' => $player->id,
        ]);
    });
});

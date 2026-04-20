<?php

use App\Models\Game;
use App\Models\Player;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

describe('GET /api/users/me', function () {
    it('returns authenticated user profile', function () {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;

        $this->withToken($token)
            ->getJson('/api/users/me')
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => ['id', 'email', 'username', 'firstname', 'lastname'],
            ]);
    });

    it('returns 401 without token', function () {
        $this->getJson('/api/users/me')->assertStatus(401);
    });
});

describe('PATCH /api/users/me', function () {
    it('updates profile fields', function () {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;

        $this->withToken($token)
            ->patchJson('/api/users/me', ['username' => 'updateduser'])
            ->assertStatus(200)
            ->assertJsonPath('data.username', 'updateduser');

        $this->assertDatabaseHas('users', ['id' => $user->id, 'username' => 'updateduser']);
    });

    it('returns 401 without token', function () {
        $this->patchJson('/api/users/me', ['firstname' => 'X'])->assertStatus(401);
    });
});

describe('PATCH /api/users/me/password', function () {
    it('updates password with correct current password', function () {
        $user = User::factory()->create(['password' => Hash::make('OldPass1')]);
        $token = $user->createToken('test')->plainTextToken;

        $this->withToken($token)
            ->patchJson('/api/users/me/password', [
                'current_password' => 'OldPass1',
                'new_password' => 'NewPass1',
                'new_password_confirmation' => 'NewPass1',
            ])->assertStatus(200);

        $this->assertTrue(Hash::check('NewPass1', $user->fresh()->password));
    });

    it('fails with wrong current password', function () {
        $user = User::factory()->create(['password' => Hash::make('OldPass1')]);
        $token = $user->createToken('test')->plainTextToken;

        $this->withToken($token)
            ->patchJson('/api/users/me/password', [
                'current_password' => 'WrongPass1',
                'new_password' => 'NewPass1',
                'new_password_confirmation' => 'NewPass1',
            ])->assertStatus(422);
    });
});

describe('PATCH /api/users/me/email', function () {
    it('updates email', function () {
        $user = User::factory()->create(['password' => Hash::make('Password1')]);
        $token = $user->createToken('test')->plainTextToken;

        $this->withToken($token)
            ->patchJson('/api/users/me/email', [
                'email' => 'newemail@test.com',
                'password' => 'Password1',
            ])->assertStatus(200)
            ->assertJsonPath('data.email', 'newemail@test.com');
    });

    it('fails with duplicate email', function () {
        User::factory()->create(['email' => 'taken@test.com']);
        $user = User::factory()->create(['password' => Hash::make('Password1')]);
        $token = $user->createToken('test')->plainTextToken;

        $this->withToken($token)
            ->patchJson('/api/users/me/email', [
                'email' => 'taken@test.com',
                'password' => 'Password1',
            ])->assertStatus(422);
    });
});

describe('DELETE /api/users/me', function () {
    it('hard deletes the user account', function () {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;
        $userId = $user->id;

        $this->withToken($token)
            ->deleteJson('/api/users/me')
            ->assertStatus(200);

        $this->assertDatabaseMissing('users', ['id' => $userId]);
    });

    it('also deletes refresh tokens and access tokens', function () {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;
        $user->refreshTokens()->create([
            'token' => hash('sha256', 'sometoken'),
            'expires_at' => now()->addDays(30),
            'revoked' => false,
        ]);

        $this->withToken($token)
            ->deleteJson('/api/users/me')
            ->assertStatus(200);

        $this->assertDatabaseMissing('personal_access_tokens', ['tokenable_id' => $user->id]);
        $this->assertDatabaseMissing('refresh_tokens', ['user_id' => $user->id]);
    });
});

describe('GET /api/users/me/export', function () {
    it('exports user data with liked teams and followed players', function () {
        $game = Game::factory()->create();
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;
        $team = Team::factory()->create(['game_id' => $game->id]);
        $player = Player::factory()->create(['game_id' => $game->id]);
        $user->likedTeams()->attach($team->id, ['liked_at' => now()]);
        $user->followedPlayers()->attach($player->id, ['followed_at' => now()]);

        $this->withToken($token)
            ->getJson('/api/users/me/export')
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => ['profile', 'liked_teams', 'followed_players'],
            ]);
    });
});

describe('GET /api/users/me/likes', function () {
    it('returns liked teams', function () {
        $game = Game::factory()->create();
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;
        $team = Team::factory()->create(['game_id' => $game->id]);
        $user->likedTeams()->attach($team->id, ['liked_at' => now()]);

        $data = $this->withToken($token)
            ->getJson('/api/users/me/likes')
            ->assertStatus(200)
            ->json('data');

        expect(count($data))->toBe(1);
        expect($data[0]['id'])->toBe($team->id);
    });
});

describe('GET /api/users/me/follows', function () {
    it('returns followed players', function () {
        $game = Game::factory()->create();
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;
        $player = Player::factory()->create(['game_id' => $game->id]);
        $user->followedPlayers()->attach($player->id, ['followed_at' => now()]);

        $data = $this->withToken($token)
            ->getJson('/api/users/me/follows')
            ->assertStatus(200)
            ->json('data');

        expect(count($data))->toBe(1);
        expect($data[0]['id'])->toBe($player->id);
    });
});

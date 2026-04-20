<?php

use App\Models\RefreshToken;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('POST /api/auth/register', function () {
    it('registers a new user and returns tokens', function () {
        $response = $this->postJson('/api/auth/register', [
            'firstname' => 'John',
            'lastname' => 'Doe',
            'email' => 'john@test.com',
            'password' => 'Password1',
            'password_confirmation' => 'Password1',
            'username' => 'johndoe',
            'birthdate' => '2000-01-01',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success', 'message',
                'data' => ['user', 'access_token', 'refresh_token', 'token_type'],
            ])
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('users', ['email' => 'john@test.com']);
    });

    it('fails with duplicate email', function () {
        User::factory()->create(['email' => 'existing@test.com']);

        $this->postJson('/api/auth/register', [
            'firstname' => 'Jane',
            'lastname' => 'Doe',
            'email' => 'existing@test.com',
            'password' => 'Password1',
            'password_confirmation' => 'Password1',
            'username' => 'janedoe',
            'birthdate' => '2000-01-01',
        ])->assertStatus(422);
    });

    it('fails with weak password', function () {
        $this->postJson('/api/auth/register', [
            'firstname' => 'John',
            'lastname' => 'Doe',
            'email' => 'new@test.com',
            'password' => 'weak',
            'password_confirmation' => 'weak',
            'username' => 'newuser',
            'birthdate' => '2000-01-01',
        ])->assertStatus(422);
    });
});

describe('POST /api/auth/login', function () {
    it('logs in with valid credentials', function () {
        $user = User::factory()->create(['password' => bcrypt('Password1')]);

        $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => 'Password1',
        ])->assertStatus(200)
            ->assertJsonStructure([
                'data' => ['access_token', 'refresh_token', 'user'],
            ]);
    });

    it('fails with wrong password', function () {
        $user = User::factory()->create(['password' => bcrypt('Password1')]);

        $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => 'WrongPass1',
        ])->assertStatus(422);
    });

    it('fails with unknown email', function () {
        $this->postJson('/api/auth/login', [
            'email' => 'nobody@test.com',
            'password' => 'Password1',
        ])->assertStatus(422);
    });
});

describe('POST /api/auth/refresh', function () {
    it('returns new tokens with valid refresh token', function () {
        $user = User::factory()->create();
        $rawToken = \Illuminate\Support\Str::random(64);
        RefreshToken::create([
            'user_id' => $user->id,
            'token' => hash('sha256', $rawToken),
            'expires_at' => now()->addDays(30),
            'revoked' => false,
        ]);

        $this->postJson('/api/auth/refresh', ['refresh_token' => $rawToken])
            ->assertStatus(200)
            ->assertJsonStructure(['data' => ['access_token', 'refresh_token']]);
    });

    it('fails with expired refresh token', function () {
        $user = User::factory()->create();
        $rawToken = \Illuminate\Support\Str::random(64);
        RefreshToken::create([
            'user_id' => $user->id,
            'token' => hash('sha256', $rawToken),
            'expires_at' => now()->subDay(),
            'revoked' => false,
        ]);

        $this->postJson('/api/auth/refresh', ['refresh_token' => $rawToken])
            ->assertStatus(422);
    });

    it('fails with revoked refresh token', function () {
        $user = User::factory()->create();
        $rawToken = \Illuminate\Support\Str::random(64);
        RefreshToken::create([
            'user_id' => $user->id,
            'token' => hash('sha256', $rawToken),
            'expires_at' => now()->addDays(30),
            'revoked' => true,
        ]);

        $this->postJson('/api/auth/refresh', ['refresh_token' => $rawToken])
            ->assertStatus(422);
    });
});

describe('POST /api/auth/logout', function () {
    it('logs out authenticated user', function () {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;

        $this->withToken($token)
            ->postJson('/api/auth/logout')
            ->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseMissing('personal_access_tokens', ['tokenable_id' => $user->id]);
    });

    it('returns 401 without token', function () {
        $this->postJson('/api/auth/logout')->assertStatus(401);
    });
});

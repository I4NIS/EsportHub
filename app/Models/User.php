<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, HasUuids;

    protected $fillable = [
        'firstname',
        'lastname',
        'email',
        'password',
        'username',
        'birthdate',
        'avatar_url',
        'is_active',
        'role',
    ];

    protected $hidden = ['password'];

    protected $casts = [
        'birthdate' => 'date',
        'is_active' => 'boolean',
    ];

    public function likedTeams()
    {
        return $this->belongsToMany(Team::class, 'user_team_likes')->withPivot('liked_at');
    }
    
    public function followedPlayers()
    {
        return $this->belongsToMany(Player::class, 'user_player_follows')->withPivot('followed_at');
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function refreshTokens()
    {
        return $this->hasMany(RefreshToken::class);
    }

}

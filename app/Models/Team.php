<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Team extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'game_id',
        'name',
        'logo_url',
        'region',
        'rank',
        'earnings',
    ];

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    public function players(): HasMany
    {
        return $this->hasMany(Player::class, 'current_team_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function likedByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_team_likes', 'team_id', 'user_id')
            ->withPivot('liked_at');
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['game'] ?? null, function ($q, $game) {
            $q->whereHas('game', fn($query) => $query->where('slug', $game));
        });

        $query->when($filters['region'] ?? null, function ($q, $region) {
            $q->where('region', $region);
        });

        $query->when($filters['sort'] ?? null, function ($q, $sort) {
            if ($sort === 'earnings') {
                $q->orderBy('earnings', 'desc');
            } elseif ($sort === 'rank') {
                $q->orderBy('rank', 'asc');
            }
        });
    }
}

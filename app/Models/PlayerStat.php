<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlayerStat extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'player_id',
        'match_id',
        'match_map_id',
        'team_id',
        'region',
        'rating',
        'acs',
        'kd_ratio',
        'kast',
        'adr',
        'kpr',
        'headshot_pct',
        'clutch_pct',
    ];

    protected $casts = [
        'rating' => 'float',
        'acs' => 'float',
        'kd_ratio' => 'float',
        'kast' => 'float',
        'adr' => 'float',
        'kpr' => 'float',
        'headshot_pct' => 'float',
        'clutch_pct' => 'float',
    ];

    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    public function match(): BelongsTo
    {
        return $this->belongsTo(GameMatch::class, 'match_id');
    }

    public function matchMap(): BelongsTo
    {
        return $this->belongsTo(MatchMap::class, 'match_map_id');
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }
}

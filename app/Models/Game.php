<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'slug',
        'logo_url'
    ];

    public function teams()
    {
        return $this->hasMany(Team::class);
    }
    public function players()
    {
        return $this->hasMany(Player::class);
    }
    public function events()
    {
        return $this->hasMany(Event::class);
    }
}

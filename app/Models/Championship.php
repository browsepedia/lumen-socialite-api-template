<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Championship extends Model
{
    protected $fillable = [];

    public function teams()
    {
        return $this->belongsToMany(Team::class, 'championship_teams', 'teamId', 'championshipId')
            ->using(ChampionshipTeam::class);
    }
}

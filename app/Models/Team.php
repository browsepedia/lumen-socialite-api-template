<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{

    protected $fillable = [];

    public function homeMatches()
    {
        return $this->hasMany(Match::class, 'homeTeamId');
    }

    public function awayMatches()
    {
        return $this->hasMany(Match::class, 'awayTeamId');
    }

    public function championships()
    {
        return $this->belongsToMany(Team::class, 'championship_teams', 'championshipId', 'teamId')
            ->using(ChampionshipTeam::class);
    }
}

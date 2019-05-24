<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Match extends Model
{

    protected $fillable = [];

    public function bets()
    {
        return $this->hasMany(Bet::class);
    }

    public function homeTeam()
    {
        return $this->belongsTo(Team::class, 'homeTeamId');
    }

    public function awayTeam()
    {
        return $this->belongsTo(Team::class, 'awayTeamId');
    }
}

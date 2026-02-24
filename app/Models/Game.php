<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    protected $fillable = [
        'Oplayer',
        'Xplayer',
        'winner',
        'board',
        'turn',
        'leagelmove',
        'smallboard'
    ];
    function getOplayer()
    {
        return User::find($this->Oplayer)->only(['id', 'name']);
    }
    function getXplayer()
    {
        return User::find($this->Xplayer)->only(['id', 'name']);
    }
    function getOpponentName() 
    {
        $userId = auth()->id();
        if ($this->Oplayer === $userId) {
            return $this->getXplayer();
        } elseif ($this->Xplayer === $userId) {
            return $this->getOplayer();
        }
        return null;

        
    }
}

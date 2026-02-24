<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Events\FriendRequestSent;
use App\Models\Friend;
use App\Models\Game;

class MainController extends Controller
{
    public function home(){
        $users = User::all();
        $games = Game::where('Oplayer',auth()->id())->orWhere('Xplayer',auth()->id())->get();
        $user = auth()->user();
        $requests = Friend::where('friend_id', $user->id)
            ->where('status', 'pending')->get();
            
        
        return view('welcome',[
            'users' => $users,'requests' => $requests,'games' => $games
        ]);
    }
}

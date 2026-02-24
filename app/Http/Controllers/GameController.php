<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Game;
use Illuminate\Support\Facades\Broadcast;
use App\Events\invite;
use App\Events\move;

class GameController extends Controller
{
    public function create(User $user1,User $user2){
        if ($user1->id == $user2->id){
            return to_route('home')->with('error','You cannot play with yourself');
        }

        $table = [$user1, $user2];
        $chosen = random_int(0, 1);
        $gameId = Game::create([
            'Oplayer' => $table[$chosen]->id,
            'Xplayer' =>$table[$chosen == 0? 1 : 0 ]->id,
            'turn' => 1,
            'board' => json_encode(array_fill(0, 9, array_fill(0, 9, null))),
            'smallboard' => json_encode(array_fill(0, 9, null)),
        ])->id;
        broadcast(new invite($user1->only('id','name'),$user2,$gameId));
        return to_route('game',$gameId);
    }
    public function game(Game $game){
        return view('dashboard', ['game'=>$game,'gameId' => $game->id, 'status' => json_decode($game->board),'leagalmove' => $game->leagelmove,'board' =>json_decode($game->smallboard)]);
    }
    public function move(Request $request){
        $Game = Game::find($request->input('game_id'));
        $user = User::find(auth()->id());
        $move = $request->input('id');
        $status = json_decode($Game->board,true);
        if($Game->winner != null){
            return response()->json([
                'error' => true,
                'message' => 'Game is already finished.',
            ], 403);
        }
        if($Game->turn && $Game->Oplayer == $user->id || !$Game->turn && $Game->Xplayer == $user->id){
            return response()->json([
                'error' => true,
                'message' => 'It is not your turn.',
            ], 403);
        }
        if($status['S'.$move[1]][(int) $move[2]] != null){
            return response()->json([
                'error' => true,
                'message' => 'this position is already taken',
            ], 403);
        }
        if($Game->leagelmove != $move[1] && $Game->leagelmove != 9){
            return response()->json([
                'error' => true,
                'message' => 'You can only play in the current legal move.',
            ], 403);
        }
        broadcast(new move(User::find(auth()->id()),$Game,$move,$status));
        
    }
}

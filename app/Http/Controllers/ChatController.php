<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Events\FriendRequestSent;
use App\Models\Friend;
use App\Models\User;
use App\Models\Message;
use App\Models\Chatroom;

class ChatController extends Controller
{
    public function index($userid)

    {
        if (auth()->id() == $userid) {
            return redirect()->route('home')->with('error', 'You cannot chat with yourself.');
        }
        $user = User::find($userid);
        if (!$user) {
            return redirect()->route('home')->with('error', 'User not found.');
        }
        $messages = Message::where('sender_id', auth()->id())->orWhere('receiver_id', auth()->id())
            ->where('receiver_id', $user->id)
            ->orWhere('sender_id', $user->id)
            ->latest()
            ->take(5)
            ->get()
            ->reverse();
        $friends = Friend::where('user_id', auth()->id())
            ->orWhere('friend_id', auth()->id())
            ->get();
        
        $Romm = Chatroom::where('user1_id', auth()->id())
            ->where('user2_id', $user->id)
            ->orWhere(function ($query) use ($user) {
                $query->where('user1_id', $user->id)
                    ->where('user2_id', auth()->id());
            })
            ->first();
        
        return view('chat',[
            'friends' => $friends,
            'messages' => $messages,
            'user' => $user->only('id','name'),
            'RommId' => $Romm->id,
        ]);
    }
    function sendmessage(Request $request)
    {
        $user = auth()->user();
        $receiverId = $request->input('userId');
        $messageContent = $request->input('message');
        if (!$receiverId || !$messageContent) {
            return response()->json(['error' => 'Receiver ID and message content are required.'], 400);
        }

        if ($user->id === $receiverId) {
            return response()->json(['error' => 'You cannot send a message to yourself.'], 400);
        }
        $Romm = Chatroom::where('user1_id', auth()->id())
        ->where('user2_id', $receiverId)
        ->orWhere(function ($query) use ($receiverId) {
            $query->where('user1_id', $receiverId)
                ->where('user2_id', auth()->id());
        })
        ->first();
        $message = Message::create([
            'sender_id' => $user->id,
            'receiver_id' => $receiverId,
            'message' => $messageContent,
        ]);

        broadcast(new \App\Events\Message($messageContent,$user->id,$Romm->id,$message->created_at->diffForHumans()));

        return to_route('chat.page',$receiverId);
        
    }
    public function request(Request $request)
    {
        $user = auth()->user();
        $friend = $request->input('friend_id');
        if (!$friend) {
            return response()->json(['error' => 'Friend ID is required.'], 400);
        }
        if ($user->id === $friend) {
            return response()->json(['error' => 'You cannot send a friend request to yourself.'], 400);
        }
        if (Friend::where('user_id', $user->id)->where('friend_id', $friend)->exists()) {
            return response()->json(['error' => 'You are already friends with this user.'], 400);
         }
        $id = Friend::create([
            'user_id' => $user->id,
            'friend_id' => $friend,
        ])->id;
    
        broadcast(new FriendRequestSent($friend,$user,$id));
        return response()->json(['message' => 'Friend request sent successfully.']);
    }
    public function decline(Request $request)
    {
        $user = auth()->user();
        $id = $request->input('id');
        
        if (!$id) {
            return response()->json(['error' => 'Friend ID is required.'], 400);
        }
        
        $friendRequest = Friend::where('id', $id)->first();
        
        if ($user->id != $friendRequest->friend_id) {
            return response()->json(['error' => 'Friend not authorised.'], 404);
        }
        
        $friendRequest->delete();
        
        return response()->json(['message' => 'Friend request declined successfully.']);
    }
    function accept(Request $request)
    {
        $user = auth()->user();
        $id = $request->input('id');
        
        if (!$id) {
            return response()->json(['error' => 'Friend ID is required.'], 400);
        }
        
        $friendRequest = Friend::where('id', $id)->first();
        
        if ($user->id != $friendRequest->friend_id) {
            return response()->json(['error' => 'Friend not authorised.'], 404);
        }
        Chatroom::create([
            'user1_id' => $user->id,
            'user2_id' => $friendRequest->user_id,
        ]);
        $friendRequest->status = 'accepted';
        $friendRequest->save();
        
        return response()->json(['message' => 'Friend request accepted successfully.']);
    }
}

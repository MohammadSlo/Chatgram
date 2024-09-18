<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\Conversation;

class MessengerController extends Controller
{
    public function index($id = null)
    {

        $user = Auth::user();

        $friends = User::where('id', '<>', $user->id)->orderBy('name')->paginate();


        // $chats = $user->conversations()->with(['lastMessage',
        // 'participants' => function($builder) use ($user){
        //     $builder->where('user_id', '<>', $user->id);
        
        // }])->get();

        // $messages = [];
        // $activeChat = new Conversation();

        // if($id) {

        //     $activeChat = $chats->where('id', $id)->first();
        //     $messages = $activeChat->messages()->with('user')->get();
            
        // }  

        return view('messenger',
            [
                'friends' => $friends,
                // 'chats' => $chats,
                // 'activeChat' => $activeChat,
                // 'messages' => $messages
            ]);
    }
}


<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Recipient;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class ConversationsController extends Controller
{

    // return all conversations for the authenticated user
    public function index() {

        $user = Auth::user();
        return $user->conversations()->with([
            'lastMessage',
            'participants' => function($builder) use ($user) {
                $builder->where('id', '<>', $user->id);
            },])
            ->withCount([
                'recipients as new_messages' => function($builder) use ($user) {
                    $builder->where('recipients.user_id', '=', $user->id)
                        ->whereNull('read_at');
                }
            ])

            
            ->paginate();

    }

    public function users() {

        return User::all();
    }

    public function show($id) {

        $user = Auth::user();

        return $user->conversations()->with(['lastMessage',
        'participants' => function($builder) use ($user){
            $builder->where('user_id', '<>', $user->id);
        
        },])
        ->withCount([
            'recipients as new_messages' => function($builder) use ($user) {
                $builder-> where('recipients.user_id', '=' , $user->id)-> whereNull('read_at');
            }
        ])->findOrFail($id);
    
    }

    public function addParticipants(Request $request, Conversation $conversation) {

        $request->validate([
            'user_id' => ['required', 'int', 'exists:users,id']
        ]);

        $conversation->participants()->attach($request->post('user_id'), [
            'joined_at' => Carbon::now()
        ]);
    }

    public function removeParticipants(Request $request, Conversation $conversation) {

        $request->validate([
            'user_id' => ['required', 'int', 'exists:users,id']
        ]);

        $conversation->participants()->detach($request->post('user_id'), [
            'joined_at' => Carbon::now()
        ]);
    }

    public function markAsRead($id) {
        Recipient::where('user_id', '=', Auth::id())
        ->whereNull('read_at')
        ->whereRaw('message_id IN (SELECT id FROM messages where conversation_id = ?)', [$id])
        ->update(['read_at' => Carbon::now(),]);

        return[ 'message' => 'Messages marked as read'];
       }

       public function destroy($id) {

        Recipient::where('user_id', '=', Auth::id())
        ->whereNull('read_at')
        ->whereRaw('message_id IN (SELECT id FROM messages where conversation_id = ?)', [$id])
        ->delete();

        return[ 'message' => 'Conversation deleted'];

       }
}

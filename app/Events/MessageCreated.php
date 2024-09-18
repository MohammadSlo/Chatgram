<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use App\Models\Message; // Add the missing import statement
use Illuminate\Support\Facades\Auth;

use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var \App\Models\Message
    */
    public $message;

    /**
     * Create a new event instance.
     * @param \App\Models\Message $message
     * 
     * @return void
     */
    public function __construct(Message $message)
    {
        $this->message = $message;
    }


    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        $other_user = $this->message->conversation->participants()
        ->where('user_id', '<>', $this->message->user_id)->first();

        return [
            new PresenceChannel('Chatgram.'.$other_user->id),
        ];
    }

    public function broadcastAS() {

        return 'new-message';
    }

}

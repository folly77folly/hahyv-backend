<?php

namespace App\Events;

use Illuminate\Support\Facades\Log;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class MessageEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $message;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($new_message)
    {
        //
        $this->message = $new_message;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        $conversation_id = $this->message->conversation_id;
        $id = Auth()->user()->id;
        // return new PrivateChannel('chat-'.$conversation_id);
        return new PrivateChannel('chat-'.$this->message->recipient_id);
    }

    public function broadcastWith(){
        return [
            $this->message
        ];
    }
}

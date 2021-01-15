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
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class PostNotificationEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $postNotification;

    /**
     * The name of the queue connection to use when broadcasting the event.
     *
     * @var string
     */
    // public $connection = 'redis';
    /**
     * The name of the queue on which to place the broadcasting job.
     *
     * @var string
     */
    // public $queue = 'default';

    /**
     * Create a new event instance.
     *
     * @return void
     */
    // public $afterCommit = true;
    public function __construct($new_postNotification)
    {
        //
        $this->postNotification = $new_postNotification;

    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        Log::debug ($this->postNotification);
        // Log::debug ('notification-'.$this->postNotification->user_id);
        return new Channel('notification-'.$this->postNotification->broadcast_id);
    }

    public function broadcastWith(){
        return [
            $this->postNotification
        ];
    }
}

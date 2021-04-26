<?php

namespace App\Listeners;

use App\Models\SubscribersList;
use App\Events\unSubscribeEvent;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class unSubscribe
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //

    }

    /**
     * Handle the event.
     *
     * @param  unSubscribeEvent  $event
     * @return void
     */
    public function handle(unSubscribeEvent $event)
    {
        //
        $subscriptions = SubscribersList::where('user_id', $event->user_id)->where('expiry', '<', now())->where('is_active', 1)->update(['is_active' =>  0]);
    }
}

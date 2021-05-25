<?php

namespace App\Listeners;

use App\Events\ReferralEvent;
use App\Traits\ReferralTrait;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class Referral
{
    
    use ReferralTrait;
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
     * @param  ReferralEvent  $event
     * @return void
     */
    public function handle(ReferralEvent $event)
    {
        //
        $this->refer($event->user_id, $event->id, $event->ip_address);
    }
}

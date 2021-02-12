<?php

namespace App\Providers;

use App\Providers\Following;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class AddFollowing
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
     * @param  Following  $event
     * @return void
     */
    public function handle(Following $event)
    {
        //
    }
}

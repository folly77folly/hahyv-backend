<?php

namespace App\Listeners;

use App\User;
use App\Traits\ReferralTrait;
use Illuminate\Support\Facades\Log;
use App\Providers\UrlShortenerEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ShortenUrl
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
     * @param  UrlShortenerEvent  $event
     * @return void
     */
    public function handle(UrlShortenerEvent $event)
    {
        //
        $user = $event->user;
        $url = $this->shortUrl($event->url);
        $newUser = User::find($user->id);
        $newUser->referral_url = $url;
        $newUser->save();
    }
}

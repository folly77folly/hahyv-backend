<?php
namespace App\Traits;

use App\Jobs\SendAnnouncement;

Trait AdminJobsTrait{

    public function dispatchJob($mailDetails, $users){

        dispatch(new SendAnnouncement($mailDetails, $users))->delay(now()->addMinute(1));
    }

}
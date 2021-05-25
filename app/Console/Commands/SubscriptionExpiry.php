<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Collections\Constants;
use App\Models\SubscribersList;
use Illuminate\Console\Command;
use App\Jobs\SubscriptionExpiryJob;
use Illuminate\Support\Facades\Log;

class SubscriptionExpiry extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:subscription_expiry';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "This command is to send emails to users who's subscription is about to expire";

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $days = Constants::SUBSCRIPTION_EXPIRY_NOTIFICATION['ONE_MONTH'];
        $time = Constants::JOB_DELAY_TIME['ONE'];

        $users = SubscribersList::whereBetween('expiry',  [Carbon::now(), Carbon::now()->addDays($days)])->with('user', 'creator')->get();
        Log::alert($users);
        if($users){
            dispatch(new SubscriptionExpiryJob($users))->delay(now()->addMinute($time));
        }
    }
}

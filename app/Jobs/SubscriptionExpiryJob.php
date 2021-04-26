<?php

namespace App\Jobs;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use App\Mail\AnnouncementMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SubscriptionExpiryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $subscribers;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($subscribers)
    {
        $this->subscribers = $subscribers;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        foreach ($this->subscribers as $subscriber){
            $username = $subscriber->user->username;
            $email = $subscriber->user->email;
            $creator = $subscriber->creator->username;
            $now = Carbon::now();
            $daysTogo = $subscriber->expiry->diffInDays($now);
            $message = "Your Subscription to $creator will expire in $daysTogo day(s)";
            $mailDetails = [
                'subject' => 'Subscription Expiry Notification',
                'body' => $message
            ];
            Mail::to($email)->queue(new AnnouncementMail($mailDetails, $username));
        }
    }
}

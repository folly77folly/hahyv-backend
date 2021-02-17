<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use App\Mail\AnnouncementMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendAnnouncement implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $mailDetails;
    public $emails;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($mailDetails, $emails)
    {
        //
        $this->mailDetails = $mailDetails;
        $this->emails = $emails;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        foreach ($this->emails as $key => $email) {
            # code...
            Mail::to($email)->send(new AnnouncementMail($this->mailDetails, $key));
        }
        // Mail::to('ilori2020@yopmail.com')->send(new AnnouncementMail($this->mailDetails));
    }
}

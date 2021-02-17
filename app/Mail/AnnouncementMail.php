<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class AnnouncementMail extends Mailable
{
    use Queueable, SerializesModels;
    public $mailDetails;
    public $name;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($mailDetails, $name)
    {
        //
        $this->mailDetails = $mailDetails;
        $this->name = $name;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        // return $this->view('view.name');
        $mail_from_name = env('MAIL_FROM_NAME');
        return $this->from(env('MAIL_FROM_ADDRESS'), $mail_from_name)
                ->subject($this->mailDetails['subject'])
                ->markdown('emails.announcement');

    }
}

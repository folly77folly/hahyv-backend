<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PayoutMail extends Mailable
{
    use Queueable, SerializesModels;
    public $mailDetails;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($mailDetails)
    {
        //
        $this->mailDetails = $mailDetails;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $mail_from_name = env('MAIL_FROM_NAME');
        return $this->from(env('MAIL_FROM_ADDRESS'), $mail_from_name)
                ->subject($this->mailDetails['subject'])
                ->markdown('emails.payout');
    }
}

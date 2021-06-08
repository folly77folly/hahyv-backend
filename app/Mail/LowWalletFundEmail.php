<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LowWalletFundEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
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
                ->replyTo(env('MAIL_REPLY_TO'))
                ->subject('Low Funds In Wallets Africa Wallet!!!')
                ->markdown('emails.lowFund');
    }
}

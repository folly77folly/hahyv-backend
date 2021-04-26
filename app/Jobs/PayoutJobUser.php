<?php

namespace App\Jobs;

use App\User;
use App\Mail\PayoutMail;
use Illuminate\Bus\Queueable;
use App\Collections\Constants;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use App\Traits\WalletTransactionsTrait;
use App\Traits\EarningTransactionsTrait;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class PayoutJobUser implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, WalletTransactionsTrait, EarningTransactionsTrait;
    public $user_id;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user_id)
    {
        //
        $this->user_id = $user_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        $creator = User::find($this->user_id);
        $description ="Payout from Admin";
        $incomeDescription ="Payout charges";
        $earning_type = Constants::EARNING['WALLET'];

            $reference ="wa_pay_".time();
            # code...
            if ($creator->availableEarning > 0){
                $payAmount = \number_format($creator->availableEarning,2,'.', ',');

                DB::transaction(function () use($creator, $description, $incomeDescription, $reference, $earning_type) {
                    $amount = $creator->availableEarning;
                    $debitAmount = -1 * $amount;
                    $allEarning = $creator->allEarning;
                    $balance = -1 * ($allEarning- $amount);
                    
                    $this->creditWallet($creator->id, $amount, $description, $reference);
                    $this->debitEarning($creator->id, $debitAmount, $description, $reference, $creator->id, $earning_type, true);
                    $reference ="wa_pay_ $creator->id".time();
                    $this->debitEarning($creator->id, $balance, $incomeDescription, $reference, $creator->id, $earning_type, false);
                });
                
                //send mail to creator
                
                $mailInfo = [
                    'username' => $creator->username,
                    'amount' => $creator->availableEarning,
                    'subject' => "Weekly Payouts of Earnings",
                    'body' => "I'm Pleased to inform you that payout from earnings wallet has been moved to your wallet.
                    A total of =N= $payAmount has been sent to your wallet
                    You can withdrawal it into your bank provisioned on the system.
                    Thanks."
                ];
                Mail::to($creator->email)->queue(new PayoutMail($mailInfo));
            }

    }
}

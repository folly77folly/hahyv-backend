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

class PayoutJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, WalletTransactionsTrait, EarningTransactionsTrait;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        $creators = User::where([
            'role_id'=> 2,
            // ['availableEarning', '>', 0]
            ])->get();
        $description ="Payout from Admin";
        $incomeDescription ="Payout charges";
        $earning_type = Constants::EARNING['WALLET'];
        $s = 1;
        foreach ($creators as $creator) {
            $reference ="wa_pay".$s."_".time();
            # code...
            if ($creator->availableEarning > 0){

                // Log::debug($creator);
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
            }
            //send mail to creator
            $payAmount = \number_format($creator->availableEarning,2,'.', ',');
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
            $s = $s + 1;
        }
    }
}

<?php

namespace App\Jobs;

use App\User;
use Illuminate\Bus\Queueable;
use App\Collections\Constants;
use App\Models\CardTransaction;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use App\Traits\WalletTransactionsTrait;
use App\Traits\EarningTransactionsTrait;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class StripeFundWalletJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, WalletTransactionsTrait,EarningTransactionsTrait;
    public $paymentDetails;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($paymentDetails)
    {
        //
        $this->paymentDetails = $paymentDetails;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        $transaction = CardTransaction::where('trans_id',$this->paymentDetails->id )->first();
        // check that event is wallet funding
        if($transaction){
            if($transaction->trans_type == 1){
                $this->creditWallet($transaction->user_id, $transaction->amount, $transaction->description, $transaction->trans_id);
            }else{
                //crediting the creator wallet
                $user = User::find($transaction->user_id);
                $subscriber_username = $user->username;
                $creator_id = $transaction->user;
                $creator_description = $user->username. " Subscribed to your content.";
                $this->creditEarning($creator_id, $transaction->amount, $creator_description, $transaction->trans_id, $transaction->user_id, Constants::EARNING['CARD']);
            }
        }

    }
}

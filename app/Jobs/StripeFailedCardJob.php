<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use App\Models\CardTransaction;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class StripeFailedCardJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
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
        if($transaction){
            CardTransaction::create([
                'user_id' =>$transaction->user_id,
                'trans_id' =>"rvs_".time(),
                'description' =>"Reversal for failed Transaction",
                'amount' => -1 * $transaction->amount,
                'receipt_url' => '',
                'receipt_no' => '',
                'card_details' => $transaction->card_details,
                'trans_type' => $transaction->trans_type,
                'user' => $transaction->user_id,
            ]);
        }
    }
}

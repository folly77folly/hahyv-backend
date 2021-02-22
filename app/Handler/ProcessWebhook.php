<?php
namespace App\Handler;

//The class extends "ProcessWebhookJob" class as that is the class 
//that will handle the job of processing our webhook before we have 
//access to it.
use \Spatie\WebhookClient\ProcessWebhookJob;
use App\Models\WalletTransaction;

use App\Traits\WalletTransactionsTrait;

use Illuminate\Support\Facades\Log;


class ProcessWebhook extends ProcessWebhookJob

{
    use WalletTransactionsTrait;

    public function handle(){

       $data = json_decode($this->webhookCall, true);
       //Do something with the event
       if($data['payload']['event'] == "transfer.failed" || $data['payload']['event'] == "transfer.reversed"){
        $reference = $data['payload']['data']['reference'];
        $transaction = WalletTransaction::where('reference', $reference)->first();
        if ($transaction){
            $description ="Reversal of transfer for $reference";
            WalletTransaction::where('reference', $reference)->update(['status'=> 0]);
            $this->creditWallet($transaction->user_id, $transaction->amountDebited, $description);
        }
       }
       //Acknowledge you received the response}
    //    http_response_code(200); 
    }   
}
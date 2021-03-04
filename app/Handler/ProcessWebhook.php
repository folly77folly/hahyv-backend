<?php
namespace App\Handler;

//The class extends "ProcessWebhookJob" class as that is the class 
//that will handle the job of processing our webhook before we have 
//access to it.
use \Spatie\WebhookClient\ProcessWebhookJob;
use Illuminate\Support\Facades\Log;

use App\Models\CardTransaction;

use App\Collections\Constants;

use App\Models\WalletTransaction;

use App\Traits\WalletTransactionsTrait;


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

    if($data['payload']['event'] == "charge.success"){
        Log::debug($data['payload']['data']);
        $reference = $data['payload']['data']['reference'];
        // $transaction = WalletTransaction::where('reference', $reference)->first();

        $transaction = CardTransaction::where('trans_id', $reference )->first();

        //update card transaction info
        $authorization = $data['payload']['data']['authorization'];
        $transaction->card_details = $authorization->brand."-".$authorization->last4;
        $transaction->save();
        
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
}
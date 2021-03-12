<?php
namespace App\Handler;

//The class extends "ProcessWebhookJob" class as that is the class 
//that will handle the job of processing our webhook before we have 
//access to it.
use \Spatie\WebhookClient\ProcessWebhookJob;
use App\Models\PostNotification;

use App\Collections\Constants;

use App\Collections\StatusCodes;

use App\Models\CardTransaction;
use App\Traits\EarningTransactionsTrait;
use App\User;
use Illuminate\Support\Facades\Log;
use App\Models\WalletTransaction;
use App\Traits\WalletTransactionsTrait;


class ProcessWebhook extends ProcessWebhookJob

{

    use WalletTransactionsTrait, EarningTransactionsTrait;


    public function handle(){
        //responding to webhook
        
        $data = json_decode($this->webhookCall, true);
        
        //Do something with the event
        if($data['payload']['event'] == "transfer.failed" || $data['payload']['event'] == "transfer.reversed"){
            http_response_code(200);
            $reference = $data['payload']['data']['reference'];
            $transaction = WalletTransaction::where('reference', $reference)->first();
            if ($transaction){
                $description ="Reversal of transfer for $reference";
                WalletTransaction::where('reference', $reference)->update(['status'=> 0]);
                $this->creditWallet($transaction->user_id, $transaction->amountDebited, $description);
            }
        }
        
        
        if($data['payload']['event'] == "charge.success")
        {
        http_response_code(200);
        $reference = $data['payload']['data']['reference'];
        $amount = ($data['payload']['data']['amount'])/100;
        $metaData = $data['payload']['data']['metadata'];

        if($metaData['user_id']){
            $authorization = $data['payload']['data']['authorization'];
        $cardTrans = CardTransaction::create([
            'user_id' =>$metaData['user_id'],
            'trans_id' =>$reference,
            'description' => $metaData['description'],
            'amount' => $amount,
            'receipt_url' => $metaData['referrer'],
            'receipt_no' => $reference,
            'card_details' => $authorization['brand']."-".$authorization['last4'],
            'trans_type' => $metaData['trans_type'],
            'user' => $metaData['creator_id']? $metaData['creator_id']:$metaData['user_id']
        ]);

        
        $transaction = CardTransaction::where(['trans_id' => $reference ])->first();
        
        // check that event is wallet funding
        if($transaction)
            {
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
}
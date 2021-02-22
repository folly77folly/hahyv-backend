<?php
namespace App\Traits;

use App\User;
use App\Models\Card;
use App\Models\HahyvEarning;
use App\Collections\Constants;
use App\Models\WalletTransaction;
use App\Models\EarningTransaction;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Api\CardTransactionController;

Trait EarningTransactionsTrait{

    public function creditEarning($id, $amount, $description, $trans_id, $sender_id, $earning_type){
        // $user = Auth()->user();
        $user = User::find($id);
        $earningBalance = $user->earningBalance;
        DB::transaction(function ()  use ($earningBalance, $user, $amount, $description, 
        $trans_id, $sender_id, $earning_type) {
    
            $user->earningBalance = $earningBalance + $amount;
            $user->save();
    
            EarningTransaction::create([
                'user_id' => $user->id,
                'description'=> $description,
                'amount' => $amount,
                'type_id' => Constants::TRANSACTION["CREDIT"],
                'trans_id' => $trans_id,
                'sender_id' => $sender_id,
                'earning_type_id' => $earning_type
            ]);

            HahyvEarning::create([
                'receiver_id' => $user->id,
                'description'=> $description,
                'amount' => $amount,
                'type_id' => Constants::TRANSACTION["CREDIT"],
                'trans_id' => $trans_id,
                'sender_id' => $sender_id,
                'earning_type_id' => $earning_type
            ]);
    
        });
    }

    public function debitEarning($id, $amount, $description, $trans_id, $sender_id, $earning_type){
        // $user = Auth()->user();
        $user = User::find($id);
        $earningBalance = $user->earningBalance;
        DB::transaction(function ()  use ($earningBalance, $user, $amount, $description,
        $trans_id, $sender_id, $earning_type) {
    
            $user->earningBalance = $earningBalance - $amount;
            $user->save();
    
            EarningTransaction::create([
                'user_id' => $user->id,
                'description'=> $description,
                'amount' => $amount,
                'type_id' => Constants::TRANSACTION["DEBIT"],
                'trans_id' => $trans_id,
                'sender_id' => $sender_id,
                'earning_type_id' => $earning_type
            ]);
    
        });
    }
    
}
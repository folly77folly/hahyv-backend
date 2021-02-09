<?php
namespace App\Traits;

use App\User;
use App\Models\Card;
use App\Collections\Constants;
use App\Models\WalletTransaction;
use App\Models\EarningTransaction;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Api\CardTransactionController;

Trait EarningTransactionsTrait{

    public function creditEarning($id, $amount, $description){
        // $user = Auth()->user();
        $user = User::find($id);
        $earningBalance = $user->earningBalance;
        DB::transaction(function ()  use ($earningBalance, $user, $amount, $description) {
    
            $user->earningBalance = $earningBalance + $amount;
            $user->save();
    
            EarningTransaction::create([
                'user_id' => $user->id,
                'description'=> $description,
                'amount' => $amount,
                'type_id' => Constants::TRANSACTION["CREDIT"],
            ]);
    
        });
    }

    public function debitEarning($id, $amount, $description){
        // $user = Auth()->user();
        $user = User::find($id);
        $earningBalance = $user->earningBalance;
        DB::transaction(function ()  use ($earningBalance, $user, $amount, $description) {
    
            $user->earningBalance = $earningBalance - $amount;
            $user->save();
    
            EarningTransaction::create([
                'user_id' => $user->id,
                'description'=> $description,
                'amount' => $amount,
                'type_id' => Constants::TRANSACTION["DEBIT"],
            ]);
    
        });
    }
    
}
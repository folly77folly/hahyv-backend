<?php
namespace App\Traits;

use App\Models\Card;
use App\Collections\Constants;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Api\CardTransactionController;
Trait WalletTransactionsTrait{

    public function creditWallet($amount, $description){
        $user = Auth()->user();
        $walletBalance = $user->walletBalance;
        DB::transaction(function ()  use ($walletBalance, $user, $amount, $description) {
    
            $user->walletBalance = $walletBalance + $amount;
            $user->save();
    
            WalletTransaction::create([
                'user_id' => $user->id,
                'description'=> $description,
                'previousWalletBalance' => $walletBalance,
                'presentWalletBalance' => $walletBalance + $amount,
                'amountCredited' => $amount,
                'amountDebited' => 0,
            ]);
    
        });
    }

    public function debitWallet($amount, $description){
        $user = Auth()->user();
        $walletBalance = $user->walletBalance;
        DB::transaction(function ()  use ($walletBalance, $user, $amount, $description) {
    
            $user->walletBalance = $walletBalance + $amount;
            $user->save();
    
            WalletTransaction::create([
                'user_id' => $user->id,
                'description'=> $description,
                'previousWalletBalance' => $walletBalance,
                'presentWalletBalance' => $walletBalance - $amount,
                'amountCredited' => 0,
                'amountDebited' => $amount,
            ]);
    
        });
    }
    
}
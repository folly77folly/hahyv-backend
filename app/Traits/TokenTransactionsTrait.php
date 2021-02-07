<?php
namespace App\Traits;

use App\User;
use App\Models\Card;
use App\Models\TokenTransaction;
use App\Collections\Constants;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Api\CardTransactionController;
Trait TokenTransactionsTrait{

    public function creditToken($id, $amount, $description){
        // $user = Auth()->user();
        $user = User::find($id);
        $walletBalance = $user->walletBalance;
        DB::transaction(function ()  use ($walletBalance, $user, $amount, $description) {
    
            $user->walletBalance = $walletBalance + $amount;
            $user->save();
    
            WalletTransaction::create([
                'user_id' => $user->id,
                'description'=> $description,
                'previousTokenBalance' => $walletBalance,
                'presentTokenBalance' => $walletBalance + $amount,
                'amountCredited' => $amount,
                'amountDebited' => 0,
            ]);
    
        });
    }

    public function debitToken($id, $amount, $description){

        $user = User::find($id);
        $tokenBalance = $user->tokenBalance;
        DB::transaction(function ()  use ($tokenBalance, $user, $amount, $description) {
    
            $user->tokenBalance = $tokenBalance - $amount;
            $user->save();
    
            TokenTransaction::create([
                'user_id' => $user->id,
                'description'=> $description,
                'previousTokenBalance' => $tokenBalance,
                'presentTokenBalance' => $tokenBalance - $amount,
                'tokenCredited' => 0,
                'tokenDebited' => $amount,
            ]);
    
        });
    }
    
}
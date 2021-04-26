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

    public function creditToken($id, $amount, $description, $reference){
        // $user = Auth()->user();
        $user = User::find($id);
        $tokenBalance = $user->tokenBalance;
        DB::transaction(function ()  use ($tokenBalance, $user, $amount, $description, $reference) {
    
            $user->tokenBalance = $tokenBalance + $amount;
            $user->save();
    
            TokenTransaction::create([
                'user_id' => $user->id,
                'description'=> $description,
                'previousTokenBalance' => $tokenBalance,
                'presentTokenBalance' => $tokenBalance + $amount,
                'tokenCredited' => $amount,
                'tokenDebited' => 0,
                'reference' => $reference,
            ]);
    
        });
    }

    public function debitToken($id, $amount, $description, $reference){

        $user = User::find($id);
        $tokenBalance = $user->tokenBalance;
        DB::transaction(function ()  use ($tokenBalance, $user, $amount, $description, $reference) {
    
            $user->tokenBalance = $tokenBalance - $amount;
            $user->save();
    
            TokenTransaction::create([
                'user_id' => $user->id,
                'description'=> $description,
                'previousTokenBalance' => $tokenBalance,
                'presentTokenBalance' => $tokenBalance - $amount,
                'tokenCredited' => 0,
                'tokenDebited' => $amount,
                'reference' => $reference,
            ]);
    
        });
    }
    
}
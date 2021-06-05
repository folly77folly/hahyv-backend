<?php
namespace App\Traits;

use App\User;
use App\Models\Card;
use App\Collections\Constants;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Api\CardTransactionController;
Trait WalletTransactionsTrait{

    public function creditWallet($id, $amount, $description, $reference = null, $transfer_code = null){
        // $user = Auth()->user();
        $user = User::find($id);
        $walletBalance = $user->walletBalance;
        DB::transaction(function ()  use ($walletBalance, $user, $amount, $description, $reference, $transfer_code) {
    
            $user->walletBalance = $walletBalance + $amount;
            $user->save();
    
            WalletTransaction::create([
                'user_id' => $user->id,
                'description'=> $description,
                'previousWalletBalance' => $walletBalance,
                'presentWalletBalance' => $walletBalance + $amount,
                'amountCredited' => $amount,
                'amountDebited' => 0,
                'reference' => $reference,
                'transfer_code' => $transfer_code
            ]);
    
        });
    }

    public function debitWallet($id, $amount, $description, $reference = null, $transfer_code = null, $status=1){
        // $user = Auth()->user();
        $user = User::find($id);
        $walletBalance = $user->walletBalance;
        DB::transaction(function ()  use ($walletBalance, $user, $amount, $description, $reference, $transfer_code, $status) {
    
            $user->walletBalance = $walletBalance - $amount;
            $user->save();
    
            WalletTransaction::create([
                'user_id' => $user->id,
                'description'=> $description,
                'previousWalletBalance' => $walletBalance,
                'presentWalletBalance' => $walletBalance - $amount,
                'amountCredited' => 0,
                'amountDebited' => $amount,
                'reference' => $reference,
                'transfer_code' => $transfer_code,
                'status' => $status,
            ]);
    
        });
    }

    public function updateTransfer($refNo)
    {
        WalletTransaction::where('reference', $refNo)->update(['status' => 1]);
        Log::info('transfer done');
    }

    public function doReversal($refNo)
    {
        $transaction = WalletTransaction::where('reference', $refNo)->where('status', 0)->first();
        if($transaction){
            $this->creditWallet($transaction->user_id, $transaction->amountDebited, 'reversal of failed transfer', 'rvsl-'.$transaction->reference, $transaction->reference);
            Log::info('reversal done');
        }
    }
    
}
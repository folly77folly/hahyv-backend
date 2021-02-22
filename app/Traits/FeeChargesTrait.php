<?php
namespace App\Traits;

use App\Models\TransactionFee;
use Illuminate\Support\Facades\Log;
Trait FeeChargesTrait{


    
    public function transactionFee(){
        $transactionFee =  TransactionFee::first();
        if(!$transactionFee){
            return 0;
        }
        if (!$transactionFee->status){
            return 0;
        }
       return  $transactionFee->amount;
    }

    
}
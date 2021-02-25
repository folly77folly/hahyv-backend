<?php

namespace App\Jobs;

use App\User;
use Illuminate\Bus\Queueable;
use App\Collections\Constants;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use App\Traits\WalletTransactionsTrait;
use App\Traits\EarningTransactionsTrait;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class PayoutJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, WalletTransactionsTrait, EarningTransactionsTrait;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        $creators = User::where([
            'is_monetize'=> 1,
            // ['availableEarning', '>', 0]
            ])->get();
        $description ="Payout from Admin";
        $incomeDescription ="Payout charges";
        $earning_type = Constants::EARNING['WALLET'];
        $s = 1;
        foreach ($creators as $creator) {
            $reference ="wa_pay".$s;
            # code...
            if ($creator->availableEarning > 0){

                // Log::debug($creator);
                DB::transaction(function () use($creator, $description, $incomeDescription, $reference, $earning_type) {
                    $amount = $creator->availableEarning;
                    $debitAmount = -1 * $amount;
                    $allEarning = $creator->allEarning;
                    $balance = -1 * ($allEarning- $amount);
                    
                    $this->creditWallet($creator->id, $amount, $description, $reference);
                    $this->debitEarning($creator->id, $debitAmount, $description, $reference, $creator->id, $earning_type, true);
                    $reference ="wa_pay_ $creator->id".time();
                    $this->debitEarning($creator->id, $balance, $incomeDescription, $reference, $creator->id, $earning_type, false);
                });
            }
            $s = $s + 1;
        }
    }
}

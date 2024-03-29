<?php
namespace App\Traits;

use App\User;
use App\Models\Referral;

use App\Collections\Constants;
use App\Models\ReferEarnSetup;
use Illuminate\Support\Facades\DB;
Trait ReferralTrait{

    use EarningTransactionsTrait;

    public function refer($user, $token, $ip_address){
        // $user = Auth()->user();
        $tokenOwner = User::where('rf_token', $token)->first();
        if(!$tokenOwner){
            Log::alert("message");
        }else if($ip_address == $tokenOwner->ip_address)
        {
            Log::alert("messages");
        }
        else{
            $data =[
                'user_id' => $tokenOwner->id,
                'referred_id' => $user
            ];
            DB::transaction(function () use($data){
                Referral::firstOrCreate($data);
                //automatic earning of money or token by user
                // $this->earn($data);
            });
        }
    }

    public function shortUrl($longUrl)
    {
        $url = urlencode($longUrl);
        $json = file_get_contents("https://cutt.ly/api/api.php?key=43034c0f0caf3b6ac9df465befb862a770633&short=$url");
        $data = json_decode ($json, true);
        if($data['url']['status'] ==7){
            return $data['url']['shortLink'];
        }else{
            return null;
        } ;
    
    }

    public function earn(array $data){
        $setup = ReferEarnSetup::first();
        if($setup->amount == 0){

        }else{
            $amount = $setup->amount;
            $number = $setup->number_to_refer;
            $noReferred = Referral::where('user_id', $data['user_id'])->count();
            $mod = fmod($noReferred, $number);
            $description = "earnings from referral";
            $reference = "ref_".time();
            if ($mod == 0){
                $this->creditEarning($data['user_id'], $amount, $description, $reference, $data['referred_id'], Constants::EARNING['REFERRAL']);
            }
        }

    }

    public function referrals($user_id)
    {
        return Referral::select('referred_id')->where('user_id', $user_id)->with('referredUser')->get();
    }

    public function referralsCount($user_id)
    {
        return Referral::where('user_id', $user_id)->count();
    }

    // public function debitWallet($id, $amount, $description){
    //     // $user = Auth()->user();
    //     $user = User::find($id);
    //     $walletBalance = $user->walletBalance;
    //     DB::transaction(function ()  use ($walletBalance, $user, $amount, $description) {
    
    //         $user->walletBalance = $walletBalance - $amount;
    //         $user->save();
    
    //         WalletTransaction::create([
    //             'user_id' => $user->id,
    //             'description'=> $description,
    //             'previousWalletBalance' => $walletBalance,
    //             'presentWalletBalance' => $walletBalance - $amount,
    //             'amountCredited' => 0,
    //             'amountDebited' => $amount,
    //         ]);
    
    //     });
    // }
    
}
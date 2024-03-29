<?php
namespace App\Traits;

use App\User;
use App\Models\Fan;

use App\Models\Bookmark;
use App\Models\Follower;
use App\Models\Referral;
use App\Collections\Constants;
use App\Models\ReferEarnSetup;
use App\Models\SubscribersList;
use App\Models\SubscriptionRate;
use App\Models\TokenTransaction;
use App\Models\PaymentPercentage;
use App\Models\WalletTransaction;
use App\Models\EarningTransaction;
use Illuminate\Support\Facades\DB;
Trait FollowingFanTrait{

 

    public function check($user_id){
        if(Auth()->user()->id != $user_id ){

            $following = Follower::where([
                'user_id'=> Auth()->user()->id,
                'following_userId'=> $user_id
                ])->first();
            
            $fan = Fan::where([
                'user_id'=> Auth()->user()->id,
                'creator_id'=> $user_id
                ])->first();
            
            if(!$following && !$fan){
                return false;
            }else{
                return true;
            }
        }

        return true;
    }

    public function subscribed($user_id){
        if(!Auth()->user()){
            return false;
        }
        if(Auth()->user()->id != $user_id ){

            $subscribed = SubscribersList::where([
                'user_id'=> Auth()->user()->id,
                'creator_id'=> $user_id,
                'is_active' => true
                ])->first();
                        
            if(!$subscribed){
                return false;
            }else{
                return true;
            }
        }

        return true;
    }

    public function bookmark($post_id){
        
        $bookmark = Bookmark::where([
            'user_id'=> Auth()->user()->id,
            'post_id'=> $post_id
            ])->first();
                    
        if(!$bookmark){
            return false;
        }else{
            return true;
        }
    }

    // public function shortUrl($longUrl)
    // {
    //     $url = urlencode($longUrl);
    //     $json = file_get_contents("https://cutt.ly/api/api.php?key=43034c0f0caf3b6ac9df465befb862a770633&short=$url");
    //     $data = json_decode ($json, true);
    //     if($data['url']['status'] ==7){
    //         return $data['url']['shortLink'];
    //     }else{
    //         return null;
    //     } ;
    
    // }

    // public function earn(array $data){
    //     $setup = ReferEarnSetup::first();
    //     if($setup->amount == 0){

    //     }else{
    //         $amount = $setup->amount;
    //         $number = $setup->number_to_refer;
    //         $noReferred = Referral::where('user_id', $data['user_id'])->count();
    //         $mod = fmod($noReferred, $number);
    //         $description = "earnings from referral";
    //         $reference = "ref".time();
    //         if ($mod == 0){
    //             $this->creditEarning($data['user_id'], $amount, $description, $reference, Auth()->user()->id, Constants::EARNING['REFERRAL']);
    //         }
    //     }

    // }

    public function subRate($id){
        $subscriptions = SubscriptionRate::where('user_id', $id)->orderBy('amount','ASC')->first();
        if ($subscriptions){
                return $subscriptions->amount;
        }
            
            return 0;
    }

    public function earnRate(){
        $amount = PaymentPercentage::first();
        if ($amount){
            $rate = number_format(($amount->rate)/100, 2);
            return floatval($rate);
        }
            
        return 1;
    }

    public function walletValue($user_id){
        if(!Auth()->user()){
            return 0;
        }
        $credit = WalletTransaction::where('user_id', $user_id)->sum('amountCredited');
        $debit = WalletTransaction::where('user_id', $user_id)->sum('amountDebited');
        $balance = $credit - $debit;
        if ($balance <= 0){
            return 0;
        }
        return Round($balance,2);
    }

    public function tokenValue($user_id){
        if(!Auth()->user()){
            return 0;
        }
        $credit = TokenTransaction::where('user_id', $user_id)->sum('tokenCredited');
        $debit = TokenTransaction::where('user_id', $user_id)->sum('tokenDebited');
        $balance = $credit - $debit;
        if ($balance <= 0){
            return 0;
        }
        // User::where('id',$user_id)->update(['walletBalance' => $balance]);
        return Round($balance,2);
    }

    public function sumEarnings($user_id)
    {
       $balance =  EarningTransaction::where('user_id', $user_id)->sum('amount');
        if ($balance <= 0){
            return 0;
        }
        // User::where('id',$user_id)->update(['walletBalance' => $balance]);
        return Round($balance,2);
    }
    
    public function follow($user_id){
        if(!Auth()->user()){
            return false;
        }
        $id = [$user_id];
        $followings = Follower::select('following_userId' )->where('user_id', '=', Auth()->user()->id)->whereIn('following_userId', $id)->get();
        $followers= Follower::select('user_id')->where('following_userId', '=', Auth()->user()->id)->whereIn('user_id', $id)->get();
        if (count($followings) > 0 && count($followers) > 0 ){
            return true;
        }
        return false;
    }
}
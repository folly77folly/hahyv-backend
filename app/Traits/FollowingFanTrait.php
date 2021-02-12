<?php
namespace App\Traits;

use App\User;
use App\Models\Fan;

use App\Models\Bookmark;
use App\Models\Follower;
use App\Models\Referral;
use App\Models\ReferEarnSetup;
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
            if ($mod == 0){
                $this->creditEarning($data['user_id'], $amount, $description);
            }
        }

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
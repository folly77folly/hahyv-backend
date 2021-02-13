<?php

namespace App\Http\Controllers\Api;

use App\User;
use App\Models\Fan;
use Illuminate\Http\Request;
use App\Collections\Constants;
use Illuminate\Support\Carbon;
use App\Models\SubscribersList;
use App\Collections\StatusCodes;
use App\Models\SubscriptionRate;
use App\Traits\CardPaymentTrait;
use App\Http\Requests\TipRequest;
use App\Http\Controllers\Controller;
use App\Http\Requests\WalletRequest;
use App\Http\Requests\SubscribeRequest;
use App\Traits\WalletTransactionsTrait;
use App\Traits\EarningTransactionsTrait;
use App\Http\Controllers\Api\PostNotificationController;

class SubscribeController extends Controller
{
    //
    use CardPaymentTrait, WalletTransactionsTrait, EarningTransactionsTrait;

    public function __construct(){
        $this->middleware('subscribe', ['only'=>['withCard', 'withWallet']]);
        $this->middleware('wallet_balance', ['only'=>['withWallet']]);
        $this->middleware('walletTipBalance', ['only'=>['tipWithWallet']]);
    }
    public function withCard(SubscribeRequest $request){
        $validatedData = $request->validated();
        $creator_id = $validatedData['creator_id'];
        $card_id = $validatedData['card_id'];
        $subscriber_username = Auth()->user()->username;
        $user = User::find($creator_id);
        $subscription = SubscriptionRate::find($validatedData['subscription_id']);
        $creator_description = "$subscriber_username Subscribed to your content";
        $description = "Subscribed to $user->username content";
        $validatedData['description'] = $description;
        $validatedData['card_id'] = $card_id;
        $validatedData['amount'] = $subscription->amount;
        $stripe = new \Stripe\StripeClient("sk_test_51I8w9TFSTnpmya5shzFVTfBqBl5hefx32VScM5aZJfSOysNrnhMF9qtcKtnywOqbvHpRd5F6ZprE04wmYll0Cxyl00hzXI0HH5");
        $response = $this->chargeCard($validatedData, $stripe);

        if ($response['code'] == 0){
            $commonFunction = new CommonFunctionsController;
            $array_json_return =$commonFunction->api_default_fail_response(__function__, $response['result']);
            return response()->json($array_json_return, StatusCodes::BAD_REQUEST);
        }

        //crediting the creator wallet
        $this->creditEarning($creator_id, $validatedData['amount'], $creator_description);

        $this->store([
            'user_id' =>Auth()->user()->id,
             'creator_id' => $creator_id
            ]);
        return response()->json([
            "status" => "success",
            "status_code" => StatusCodes::SUCCESS,
            "message" => "subscription successful.",
        ],StatusCodes::SUCCESS);
    }

    public function withWallet(WalletRequest $request){
        $validatedData = $request->validated();
        $creator_id = $validatedData['creator_id'];
        $user = User::find($creator_id);
        $subscription = SubscriptionRate::find($validatedData['subscription_id']);
        $subscriber_username = Auth()->user()->username;
        $description = "Subscribed to $user->username content";
        $creator_description = "$subscriber_username Subscribed to your content";
        $amount =$subscription->amount;

        $this->debitWallet(Auth()->user()->id,$amount, $description);
        //crediting the creator wallet
        $this->creditEarning($creator_id,$amount, $creator_description);


        $this->store([
            'user_id' =>Auth()->user()->id,
             'creator_id' => $creator_id,
             'period' => $subscription->subscription->period,
             'expiry' => Carbon::now()->addMonths($subscription->subscription->period)
            ]);
        return response()->json([
            "status" => "success",
            "status_code" => StatusCodes::SUCCESS,
            "message" => "subscription successful.",
        ],StatusCodes::SUCCESS);

    }

    public function store(array $data){
        //check is subscription exists
        $creator_id = $data['creator_id'];
        $subscription = SubscribersList::where([
            'user_id'=> Auth()->user()->id,
            'creator_id'=> $creator_id,
            ])->first();
        if($subscription){
            if (!$subscription->is_active){
                $subscription->is_active = 1;
                $subscription->save();
            }
        }else{

            SubscribersList::firstOrCreate($data);
            $fanData = [
                'user_id' =>$data['user_id'],
                'creator_id' => $data['creator_id'],
            ];
            Fan::firstOrCreate($fanData);
        }
        $this->notify(Auth()->user()->username, $creator_id, 'subscribes');
    }

    public function notify($username, $id_other_user, $type)
    {
        $post_notify = new PostNotificationController();
        $result = $post_notify->store([
            'message'=> "$username $type to your content",
            'user_id' => Auth()->user()->id,
            'broadcast_id' => $id_other_user,
            'post_type_id' => Constants::NOTIFICATION["SUBSCRIBED"]
        ]);
    }

    public function tipWithWallet(TipRequest $request){
        $validatedData = $request->validated();
        
        $creator_id = $validatedData['creator_id'];
        $amount = $validatedData['amount'];
        $user = User::find($creator_id);
        $subscriber_username = Auth()->user()->username;
        $description = "Tip sent to $user->username for free";
        $creator_description = "$subscriber_username tipped your post";

        $this->debitWallet(Auth()->user()->id,$amount, $description);
        $this->creditEarning($creator_id,$amount, $creator_description);

        $data = [
            'user_id' =>Auth()->user()->id,
            'creator_id' => $creator_id
            ];
        $fan = Fan::firstOrCreate($data);

        $this->notify(Auth()->user()->username, $creator_id, 'tipped');

        return response()->json([
            "status" => "success",
            "status_code" => StatusCodes::SUCCESS,
            "message" => "tip successfully made.",
        ],StatusCodes::SUCCESS);

    }
}

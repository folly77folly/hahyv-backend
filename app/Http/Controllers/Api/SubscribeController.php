<?php

namespace App\Http\Controllers\Api;

use App\User;
use App\Models\Fan;
use Illuminate\Http\Request;
use App\Collections\Constants;
use App\Models\SubscribersList;
use App\Collections\StatusCodes;
use App\Traits\CardPaymentTrait;
use App\Http\Controllers\Controller;
use App\Http\Requests\WalletRequest;
use App\Http\Requests\SubscribeRequest;
use App\Traits\WalletTransactionsTrait;
use App\Http\Controllers\Api\PostNotificationController;

class SubscribeController extends Controller
{
    //
    use CardPaymentTrait, WalletTransactionsTrait;

    public function __construct(){
        $this->middleware('subscribe', ['only'=>['withCard', 'withWallet']]);
        $this->middleware('wallet_balance', ['only'=>['withWallet']]);
    }
    public function withCard(SubscribeRequest $request){
        $validatedData = $request->validated();
        $creator_id = $validatedData['creator_id'];
        $card_id = $validatedData['card_id'];
        $user = User::find($creator_id);
        $description = "Subscribed to $user->username content";
        $validatedData['description'] = $description;
        $validatedData['card_id'] = $card_id;
        $validatedData['amount'] = $user->subscription_amount;
        $stripe = new \Stripe\StripeClient("sk_test_51I8w9TFSTnpmya5shzFVTfBqBl5hefx32VScM5aZJfSOysNrnhMF9qtcKtnywOqbvHpRd5F6ZprE04wmYll0Cxyl00hzXI0HH5");
        $response = $this->chargeCard($validatedData, $stripe);

        if ($response['code'] == 0){
            $commonFunction = new CommonFunctionsController;
            $array_json_return =$commonFunction->api_default_fail_response(__function__, $response['result']);
            return response()->json($array_json_return, StatusCodes::BAD_REQUEST);
        }
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
        $description = "Subscribed to $user->username content";
        $amount =$user->subscription_amount;

        $this->debitWallet($amount, $description);


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

            SubscribersList::create($data);
            Fan::create($data);
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
}
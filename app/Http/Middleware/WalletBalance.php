<?php

namespace App\Http\Middleware;

use Closure;
use App\User;
use App\Collections\StatusCodes;
use App\Models\SubscriptionRate;
use Illuminate\Support\Facades\Log;

class WalletBalance
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user_balance = Auth()->user()->walletBalance;
        if($user_balance == 0){
            return response()->json([
                'status' => 'failure',
                'status_code' => StatusCodes::BAD_REQUEST,
                'message'=>'Insufficient wallet balance fund your wallet'
            ],StatusCodes::BAD_REQUEST);
        }
        $subscription = SubscriptionRate::find($request->subscription_id);
        if($subscription){
            $amount = $subscription->amount;
            if($user_balance < $amount){
                    return response()->json([
                        "status" => "failure",
                        "status_code" => StatusCodes::BAD_REQUEST,
                        "message" => "wallet balance insufficient for this transaction fund your wallet.",
                    ],StatusCodes::BAD_REQUEST);
            }
        }else{
            return response()->json([
                "status" => "failure",
                "status_code" => StatusCodes::BAD_REQUEST,
                "message" => "Invalid subscription ID.",
            ],StatusCodes::BAD_REQUEST);
        }
        return $next($request);
    }
}

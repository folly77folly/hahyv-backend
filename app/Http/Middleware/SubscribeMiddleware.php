<?php

namespace App\Http\Middleware;

use Closure;
use App\User;
use App\Models\SubscribersList;
use App\Collections\StatusCodes;
use App\Models\SubscriptionRate;
use Illuminate\Support\Facades\Log;

class SubscribeMiddleware
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
        // subscriber/creator account not monetized
        $user = User::find($request->creator_id);
        if(!$user->is_monetize){
            return response()->json([
                'status' => 'failure',
                'status_code' => StatusCodes::BAD_REQUEST,
                'message'=>'This Profile is not Monetized'
            ],StatusCodes::BAD_REQUEST);
        }
        // subscriber/creator subscription amount is not set
        $subscription = SubscriptionRate::find($request->subscription_id);
        if($subscription){

            if($subscription->amount == 0){
                return response()->json([
                    'status' => 'failure',
                    'status_code' => StatusCodes::BAD_REQUEST,
                    'message'=>'This user subscription amount is not set'
                ],StatusCodes::BAD_REQUEST);
            }
        }
        // checking is subscription exists
        $subscription = SubscribersList::where([
            'user_id'=> Auth()->user()->id,
            'creator_id'=> $request->creator_id,
            'is_active' => true
            ])->first();
            if($subscription){
                return response()->json([
                    'status' => 'failure',
                    'status_code' => StatusCodes::BAD_REQUEST,
                    'message'=>'This subscription is active cannot continue'
                ],StatusCodes::BAD_REQUEST);
            }
            // Can't subscribe to your own content
                $user_id = Auth()->user()->id;
                $creator_id = $request->creator_id;
                if($user_id == $creator_id){
                    return response()->json([
                        'status' => 'failure',
                        'status_code' => StatusCodes::BAD_REQUEST,
                        'message'=>"Can't subscribe to your own content"
                    ],StatusCodes::BAD_REQUEST);
                }
        return $next($request);
    }
}

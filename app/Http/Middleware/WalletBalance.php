<?php

namespace App\Http\Middleware;

use Closure;
use App\User;
use App\Collections\StatusCodes;
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
        $creator = User::find($request->creator_id);
        $creator_balance = $creator->subscription_amount;
        if($user_balance < $creator_balance){
            return response()->json([
                'status' => 'failure',
                'status_code' => StatusCodes::BAD_REQUEST,
                'message'=>'Insufficient wallet balance'
            ],StatusCodes::BAD_REQUEST);
        }
        return $next($request);
    }
}

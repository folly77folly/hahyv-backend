<?php

namespace App\Http\Middleware;

use Closure;
use App\Collections\StatusCodes;

class WalletTipBalance
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
        if(Auth()->user()->walletBalance < $request->amount){
            return response()->json([
                "status" => "failure",
                "status_code" => StatusCodes::BAD_REQUEST,
                "message" => "wallet balance insufficient for this transaction fund your wallet.",
            ],StatusCodes::BAD_REQUEST);
    }
        return $next($request);
    }
}

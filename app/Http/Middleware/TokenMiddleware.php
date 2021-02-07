<?php

namespace App\Http\Middleware;

use Closure;
use App\Collections\StatusCodes;

class TokenMiddleware
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
        $user=Auth()->user();
        // subscriber/creator subscription amount is not set
        if($user->tokenBalance == 0){
            return response()->json([
                'status' => 'failure',
                'status_code' => StatusCodes::BAD_REQUEST,
                'message'=>'You need token to send message'
            ],StatusCodes::BAD_REQUEST);
        }
        return $next($request);
    }
}

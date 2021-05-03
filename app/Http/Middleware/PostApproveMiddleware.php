<?php

namespace App\Http\Middleware;

use Closure;
use App\Collections\StatusCodes;

class PostApproveMiddleware
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

        // Prevent Posting when user has not filled bio and bank details
        // if(!Auth()->user()->profile_image_url){
        //     return response()->json([
        //         'status'=> 'failure',
        //         'status_code'=> StatusCodes::BAD_REQUEST,
        //         'message'=>"Kindly Update your Profile and Wallet Details(Bank) before Posting",
        //     ],StatusCodes::BAD_REQUEST);
        // }elseif(!Auth()->user()->cover_image_url){
        //     return response()->json([
        //         'status'=> 'failure',
        //         'status_code'=> StatusCodes::BAD_REQUEST,
        //         'message'=>"Kindly Update your Profile and Wallet Details(Bank) before Posting"
        //     ],StatusCodes::BAD_REQUEST);
        // }elseif(!Auth()->user()->description){
        //     return response()->json([
        //         'status'=> 'failure',
        //         'status_code'=> StatusCodes::BAD_REQUEST,
        //         'message'=>"Kindly Update your Profile and Wallet Details(Bank) before Posting"
        //     ],StatusCodes::BAD_REQUEST);
        // }
        // elseif(!Auth()->user()->bankDetail){
        //     return response()->json([
        //         'status'=> 'failure',
        //         'status_code'=> StatusCodes::BAD_REQUEST,
        //         'message'=>"Kindly Update your Profile and Wallet Details(Bank) before Posting",
        //     ],StatusCodes::BAD_REQUEST);
        // }
        // else{
        //     return $next($request);
        // }

        return $next($request);
        
    }
}

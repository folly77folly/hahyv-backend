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

        if(!Auth()->user()->profile_image_url){
            return response()->json([
                'status'=> 'failure',
                'status_code'=> StatusCodes::BAD_REQUEST,
                'message'=>"You are unable to post any more content until you have uploaded a Profile Image",
            ],StatusCodes::BAD_REQUEST);
        }elseif(!Auth()->user()->cover_image_url){
            return response()->json([
                'status'=> 'failure',
                'status_code'=> StatusCodes::BAD_REQUEST,
                'message'=>"You are unable to post any more content until you have uploaded a Cover Image"
            ],StatusCodes::BAD_REQUEST);
        }elseif(!Auth()->user()->description){
            return response()->json([
                'status'=> 'failure',
                'status_code'=> StatusCodes::BAD_REQUEST,
                'message'=>"You are unable to post any more content until you fill your bio"
            ],StatusCodes::BAD_REQUEST);
        }
        elseif(!Auth()->user()->bankDetail){
            return response()->json([
                'status'=> 'failure',
                'status_code'=> StatusCodes::BAD_REQUEST,
                'message'=>"You are unable to post any more content until you have uploaded bank details",
            ],StatusCodes::BAD_REQUEST);
        }
        else{
            return $next($request);
        }
        
    }
}

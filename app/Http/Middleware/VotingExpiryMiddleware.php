<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Post;
use Illuminate\Support\Carbon;
use App\Collections\StatusCodes;

class VotingExpiryMiddleware
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
        $post = Post::find($request->post_id);
        if($post->poll_expiry < Carbon::now()){
            return response()->json([
                'status' => 'failure',
                'status_code' => StatusCodes::BAD_REQUEST,
                'message'=>'This Poll has expired'
            ],StatusCodes::BAD_REQUEST);
        }
        return $next($request);
    }
}

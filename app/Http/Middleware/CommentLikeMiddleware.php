<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Fan;
use App\Models\Post;
use App\Models\Follower;
use App\Collections\StatusCodes;

class CommentLikeMiddleware
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
        if(Auth()->user()->id != $post->user_id ){

            $following = Follower::where([
                'user_id'=> Auth()->user()->id,
                'following_userId'=> $post->user_id
                ])->first();
            
            $fan = Fan::where([
                'user_id'=> Auth()->user()->id,
                'creator_id'=> $post->user_id
                ])->first();
            
            if(!$following && !$fan){
                return response()->json([
                    'status'=> 'failure',
                    'status_code'=> StatusCodes::BAD_REQUEST,
                    'message'=>'You have to be following or subscribed.'
                ],StatusCodes::BAD_REQUEST);
            }
            
        }
        return $next($request);
    }
}

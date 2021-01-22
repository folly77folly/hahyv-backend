<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Post;
use App\Collections\StatusCodes;

class CommentMiddleware
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
        if($post->disable_comment){
            return response()->json([
                'message'=>'Comment has been disabled for this post'
            ],StatusCodes::BAD_REQUEST);
        }
        return $next($request);
    }
}

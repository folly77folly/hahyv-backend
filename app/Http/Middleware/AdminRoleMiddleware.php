<?php

namespace App\Http\Middleware;

use Closure;
use App\Collections\StatusCodes;

class AdminRoleMiddleware
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
        if(Auth()->user()->role_id == 2){
            return response()->json([
                'status'=> 'failure',
                'status_code'=> StatusCodes::UNAUTHORIZED,
                'message'=>'You are not authorized.'
            ],StatusCodes::UNAUTHORIZED);
        }
        return $next($request);
    }
}

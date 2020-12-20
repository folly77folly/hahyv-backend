<?php

namespace App\Http\Middleware;

use Closure;
use App\Collections\StatusCodes;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class EmailVerifiedMiddleWare
{
/**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $redirectToRoute
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle($request, Closure $next)
    {
        // var_dump($request->user());
        // die();
        if (! $request->user() ||
            ($request->user() instanceof MustVerifyEmail &&
            ! $request->user()->hasVerifiedEmail())) {
            return response()->json([
                "status" => "failure",
                "status_code" => StatusCodes::FORBIDDEN,
                "message" => "Your email address is not verified"
            ], StatusCodes::FORBIDDEN);
        }

        return $next($request);
    }
}

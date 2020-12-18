<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Collections\StatusCodes;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\ResetsPasswords;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    // protected $redirectTo = RouteServiceProvider::HOME;

    protected function sendResetResponse(Request $request, $response){
        return response([
            "status"=> "success",
            "status_code"=> StatusCodes::SUCCESS,
            "message"=> trans($response)
        ], StatusCodes::SUCCESS);
    }

    protected function sendResetFailedResponse(Request $request, $response){
        return response([
            "status"=> "failure",
            "status_code"=> StatusCodes::UNPROCESSABLE,
            "message"=> trans($response)
        ], StatusCodes::UNPROCESSABLE);
    }
}

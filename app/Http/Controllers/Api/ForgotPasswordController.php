<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Auth\Passwords;
use App\Collections\StatusCodes;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Passwords\PasswordBroker;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;

class ForgotPasswordController extends Controller
{
    use CanResetPassword;
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */
    public function sendPasswordResetNotification($token)
    {
        return $token;
    }

    use SendsPasswordResetEmails;

    protected function sendResetLinkFailedResponse(Request $request, $response)
    {
        return response([
            "status"=> "failure",
            "status_code"=> StatusCodes::UNPROCESSABLE,
            "message"=> trans($response)
        ], StatusCodes::UNPROCESSABLE);
    }


    protected function sendResetLinkResponse(Request $request, $response)
    {
        $user = DB::table('users')->where('email', '=', $request->email)->first();
        return response([
            "status"=> "success",
            "status_code"=> StatusCodes::SUCCESS,
            "password_reset_token" => $user->provider_id,
            "message"=> trans($response)
        ], StatusCodes::SUCCESS);
    }
}

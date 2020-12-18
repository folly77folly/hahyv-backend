<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use App\Collections\StatusCodes;

class ForgotPasswordController extends Controller
{
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
        return response([
            "status"=> "success",
            "status_code"=> StatusCodes::SUCCESS,
            "message"=> trans($response)
        ], StatusCodes::SUCCESS);
    }
}

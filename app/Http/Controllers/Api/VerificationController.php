<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Collections\StatusCodes;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Auth\Access\AuthorizationException;

class VerificationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Email Verification Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling email verification for any
    | user that recently registered with the application. Emails may also
    | be re-sent if the user didn't receive the original email message.
    |
    */

    use VerifiesEmails;

    /**
     * Where to redirect users after verification.
     *
     * @var string
     */
    // protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api')->only('resend');
        $this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('verify', 'resend');
    }

    public function verify(Request $request)
    {
        auth()->loginUsingId($request->route('id'));
        if (! hash_equals((string) $request->route('id'), (string) $request->user()->getKey())) {
            throw new AuthorizationException;  
        }

        if (! hash_equals((string) $request->route('hash'), sha1($request->user()->getEmailForVerification()))) {
            throw new AuthorizationException;
        }

        if ($request->user()->hasVerifiedEmail()) {
            return response([
                "status"=> "failure",
                "status_code" => StatusCodes::BAD_REQUEST,
                "message"=>"Already Verified"
            ],StatusCodes::BAD_REQUEST);   
        }

        // if ($request->user()->markEmailAsVerified()) {
        //     event(new Verified($request->user()));
        // }

        // if ($response = $this->verified($request)) {
        //     return response(["message"=>"Successfully Verified"]);
        // }

        return response([
            "status"=> "success",
            "status_code" => StatusCodes::SUCCESS,
            "message"=>"Successfully Verified"
        ],StatusCodes::SUCCESS);
    }

    public function resend(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return response([
                "status"=> "failure",
                "status_code"=>StatusCodes::BAD_REQUEST,
                "message"=>"Email Already Verified"
            ],StatusCodes::BAD_REQUEST);
        }

        $request->user()->sendEmailVerificationNotification();

        return response([
            "status"=> "success",
            "status_code"=>StatusCodes::SUCCESS,
            "message"=>"Email Sent"
        ],StatusCodes::SUCCESS);
    }
}

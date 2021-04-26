<?php

namespace App\Http\Controllers\Api;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Collections\StatusCodes;
use App\Http\Requests\OtpRequest;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
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
        $this->middleware('auth:api')->only('resend', 'verifyOtp');
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

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }


        // if ($response = $this->verified($request)) {
        //     return response(["message"=>"Successfully Verified"]);
        // }
        $base_url = env('BASE_URL', 'http://127.0.0.1:3001');
        // $token = auth()->loginUsingId($request->route('id'));
        $accessToken = Auth()->user()->createToken("authToken")->accessToken;

        return redirect($base_url."/welcome/?token=$accessToken");

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
            "message"=>"Verification Link Sent to your email address"
        ],StatusCodes::SUCCESS);
    }

    public function verifyOTP(OtpRequest $request)
    {
        $validatedData = $request->validated();

        auth()->loginUsingId($validatedData["id"]);

        if(!auth()->loginUsingId($validatedData["id"])){
            return response()->json([
                "status"=> "failure",
                "status_code"=>StatusCodes::UNPROCESSABLE,
                "message"=>"Wrong User"
            ],StatusCodes::UNPROCESSABLE);
        }

        if (request()->user()->hasVerifiedEmail()) {
            return response([
                "status"=> "failure",
                "status_code" => StatusCodes::BAD_REQUEST,
                "message"=>"Already Verified"
            ],StatusCodes::BAD_REQUEST);   
        }

        if(request()->user()->otp != $validatedData["otp"])
        {
            return response()->json([
                "status"=> "failure",
                "status_code"=>StatusCodes::UNPROCESSABLE,
                "message"=>"Validation Code is not correct"
            ],StatusCodes::UNPROCESSABLE);
        }

        $myUser = User::findOrfail(request()->user()->id);
        $myUser->email_verified_at = Carbon::now();
        $myUser->save();

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return response()->json([
            "status"=> "success",
            "status_code"=>StatusCodes::SUCCESS,
            "message"=>"User successfully verified"
        ],StatusCodes::SUCCESS);
    }
}

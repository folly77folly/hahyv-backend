<?php

namespace App\Http\Controllers\Api;

use App\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Events\ReferralEvent;
use App\Collections\Constants;
use Illuminate\Support\Carbon;
use App\Collections\StatusCodes;
use App\Http\Requests\LoginRequest;
use App\Http\Controllers\Controller;
use App\Providers\UrlShortenerEvent;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\RegisterRequest;
use Illuminate\Auth\Events\Registered;
use App\Http\Requests\ChangePasswordRequest;

class AuthController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $id = Auth()->user()->id;
        $users = User::select(
            'id',
            'name',
            'username',
            'email',
            'description',
            'profile_image_url',
            'cover_image_url',
            'preference_id',
            'created_at',
            'updated_at',
            )
            ->where('id', '!=', $id)
            ->where('role_id', '!=', 1)
            ->where('email_verified_at', '!=', null)
            ->paginate(Constants::PAGE_LIMIT);

        if(!$users){

            return response()->json([
                "status"=> "failure",
                "status_code"=> StatusCodes::UNPROCESSABLE,
                "message"=>"No users found",
                ],StatusCodes::UNPROCESSABLE);
        }

            return response()->json([
                "status"=> "success",
                "status_code"=> StatusCodes::SUCCESS,
                "message"=>"Users found",
                "data"=> $users
                ],StatusCodes::SUCCESS);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function register(RegisterRequest $request)
    {
        $validatedData = $request->validated();


        $validatedData["password"] = Hash::make($request["password"]);
        $validatedData["otp"] = OTP();
        $token = Str::uuid()->toString();
        $validatedData["rf_token"] = $token;
        $url = env('BASE_URL','http://127.0.0.1:3001').'/signup/?rf_token='.$token;
        $user = User::create($validatedData);
        
        if (!empty($validatedData["provider_name"])){
            $user->email_verified_at = Carbon::now();
            $user->save();
        }
        event(new Registered($user));
        event(new UrlShortenerEvent($user, $url));
        $accessToken = $user->createToken('authToken')->accessToken;

        if (!empty($request->referral_id)){
            event(new ReferralEvent($user->id, $request->referral_id));
        }
        return response()->json([
            "status"=> "success",
            "message"=>"Registration Successful",
            "data"=> [
                "name"=>$user->name,
                "username"=>$user->username,
                "email"=>$user->email,
                "id"=>$user->id,
                "provider_name" => $user->provider_name,
                "token" => $accessToken,
                "created_at" => $user->created_at,
                "updated_at" => $user->updated_at,
                "verified_at" => $user->email_verified_at,
            ]
            ],StatusCodes::CREATED);
    }

    public function login(LoginRequest $request)
    {

        $validatedData = $request->validated();


        if (!Auth()->attempt($validatedData)){
            return response()->json([
                "status"=>"failure",
                "status_code" => StatusCodes::UNAUTHORIZED,
                "message"=>"Invalid Email or Password"
            ], StatusCodes::UNAUTHORIZED);
        }


        if(!Auth()->user()->hasVerifiedEmail()){
            return response()->json([
                "status"=>"failure",
                "status_code" => StatusCodes::UNAUTHORIZED,
                "message"=>"Email has not been Verified"
            ], StatusCodes::UNAUTHORIZED);
        }

        if(!Auth()->user()->is_active){
            return response()->json([
                "status"=>"failure",
                "status_code" => StatusCodes::UNAUTHORIZED,
                "message"=>"Your account is inactive"
            ], StatusCodes::UNAUTHORIZED);
        }

        $accessToken = Auth()->user()->createToken("authToken")->accessToken;
        return response()->json([
            "status"=>"success",
            "status_code"=> StatusCodes::SUCCESS,
            "name"=> Auth()->user()->name,
            "username"=> Auth()->user()->username,
            "email"=> Auth()->user()->email,
            "id"=> Auth()->user()->id,
            "provider_name"=> Auth()->user()->provider_name,
            "token" => $accessToken,
            "verified_at" => Auth()->user()->email_verified_at
        ],StatusCodes::SUCCESS);
    }

    public function changePassword(ChangePasswordRequest $request)
    {       
            $validatedData = $request->validated();

            $user = User::find(Auth()->user()->id);

           if(!Hash::check($validatedData["current_password"], $user->password)){
               return response()->json([
                   "status" =>"failure",
                   "status_code" => StatusCodes::UNPROCESSABLE,
                   "message" => "Invalid current password",
               ],StatusCodes::UNPROCESSABLE,);
           }

            $newPassword = $validatedData['new_password'];
            $user->password = Hash::make($newPassword);
            $user->save();
            return response()->json([
                "status" => "success",
                "status_code" => StatusCodes::SUCCESS,
                "message" => "Password changed successfully",
            ],StatusCodes::SUCCESS,);
    }

    public function welcome(Request $request){

        $user = $request->user();
        if (!$user){
            
            return response()->json([
                "status"=>"failure",
                "status_code" => StatusCodes::UNAUTHORIZED,
                "message"=>"You are not authorized"
            ], StatusCodes::UNAUTHORIZED);
        }
        return response()->json([
            "status" => "success",
            "status_code" => StatusCodes::SUCCESS,
            "message" => "user login successfully",
            "data"=> [
                "name"=>$user->name,
                "username"=>$user->username,
                "email"=>$user->email,
                "id"=>$user->id,
                "created_at" => $user->created_at,
                "updated_at" => $user->updated_at,
                "verified_at" => $user->email_verified_at,
            ]
        ],StatusCodes::SUCCESS,);
    }
}

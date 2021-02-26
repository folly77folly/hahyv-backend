<?php

namespace App\Http\Controllers\Api\Admin;

use App\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Collections\StatusCodes;
use App\Http\Requests\LoginRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\ChangePasswordRequest;

class AuthController extends Controller
{
    //
    public function register(RegisterRequest $request)
    {
        $validatedData = $request->validated();


        $validatedData["password"] = Hash::make($request["password"]);
        $validatedData["otp"] = OTP();
        $validatedData["role_id"] = 1;
        $token = Str::uuid()->toString();
        $validatedData["rf_token"] = $token;
        $validatedData["email_verified_at"] = Carbon::now();
        $url = env('BASE_URL','http://127.0.0.1:3001').'/signup/?rf_token='.$token;
        $user = User::create($validatedData);
        
        // event(new Registered($user));
        $accessToken = $user->createToken('authToken')->accessToken;

        return response()->json([
            "status"=> "success",
            "message"=>"Registration Successful",
            "data"=> [
                "name"=>$user->name,
                "username"=>$user->username,
                "email"=>$user->email,
                "id"=>$user->id,
                "token" => $accessToken,
                "created_at" => $user->created_at,
                "updated_at" => $user->updated_at,
                "verified_at" => $user->email_verified_at
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

        if(Auth()->user()->role_id != 1){
            return response()->json([
                "status"=>"failure",
                "status_code" => StatusCodes::UNAUTHORIZED,
                "message"=>"You are not allowed"
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
}

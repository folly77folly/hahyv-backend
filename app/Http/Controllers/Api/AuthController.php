<?php

namespace App\Http\Controllers\Api;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Collections\StatusCodes;
use App\Http\Requests\LoginRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\RegisterRequest;
use Illuminate\Auth\Events\Registered;

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
        $users = User::all(
            'id',
            'name',
            'username',
            'email',
            'description',
            'profile_image_url',
            'cover_image_url',
            'preference_id'
            )
            ->where('id', '!=', $id)
            ->toArray();

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
                "data"=> $users //array_values($users)
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
        $user = User::create($validatedData);
        
        if (!empty($validatedData["provider_name"])){
            $user->email_verified_at = Carbon::now();
            $user->save();
        }
        event(new Registered($user));
        $accessToken = $user->createToken('authToken')->accessToken;
        return response()->json([
            "status"=> "success",
            "message"=>"Registration Successful",
            "data"=> [
                "name"=>$user->name,
                "username"=>$user->username,
                "email"=>$user->email,
                "id"=>$user->id,
                "token" => $accessToken
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

        $accessToken = Auth()->user()->createToken("authToken")->accessToken;
        return response()->json([
            "status"=>"success",
            "status_code"=> StatusCodes::SUCCESS,
            "name"=> Auth()->user()->name,
            "username"=> Auth()->user()->username,
            "email"=> Auth()->user()->email,
            "id"=> Auth()->user()->id,
            "token" => $accessToken
        ],StatusCodes::SUCCESS);
    }

}

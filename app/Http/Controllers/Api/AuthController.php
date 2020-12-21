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

        var_dump($validatedData["provider_name"]);

        $validatedData["password"] = Hash::make($request["password"]);
        $validatedData["otp"] = OTP();
        $user = User::create($validatedData);
        
        if (!empty($validatedData["provider_name"])){
            $user->email_verified_at = Carbon::now();
            $user->save();
        }
        $accessToken = $user->createToken('authToken')->accessToken;
        return response()->json([
            "status"=> "success",
            "message"=>"Registration Successful",
            "data"=> [
                "user"=>$user,
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

        // var_dump(Auth()->user());
        // die();

        if(!Auth()->user()->email_verified_at){
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
            "user"=> Auth()->user(),
            "token" => $accessToken
        ],StatusCodes::SUCCESS);
    }

}

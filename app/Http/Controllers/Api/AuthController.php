<?php

namespace App\Http\Controllers\Api;

use App\User;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\RegisterRequest;
use App\Collections\StatusCodes;

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
        $validatedData["password"] = Hash::make($request["password"]);
        $user = User::create($validatedData);
        $accessToken = $user->createToken('authToken')->accessToken;
        return response()->json([
            "status"=> 1,
            "message"=>"Registration Successful",
            "data"=> [
                "user"=>$user,
                "token" => $accessToken
            ]
            ],201);
    }

    public function login(LoginRequest $request)
    {
        $validatedData = $request->validated();

        if (!Auth()->attempt($validatedData)){
            return response()->json([
                "status"=>"failure",
                "Message"=>"Invalid Email or Password"
            ], StatusCode::BAD_REQUEST);
        }

        $accessToken = Auth()->user()->createToken("authToken")->accessToken;
        return response()->json([
            "status"=>"success",
            "status_code"=>StatusCode::SUCCESS,
            "user"=> Auth()->user(),
            "token" => $accessToken
        ],StatusCode::SUCCESS);
    }

}

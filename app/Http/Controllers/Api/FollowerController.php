<?php

namespace App\Http\Controllers\API;


use App\User;
use App\Models\Follower;
use Illuminate\Http\Request;
use App\Collections\StatusCodes;
use App\Http\Controllers\Controller;
use App\Http\Requests\FollowerRequest;

class FollowerController extends Controller
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
    public function store(FollowerRequest $request)
    {
        //
        $validatedData = $request->validated();
        $data = [
            'user_id'=> Auth()->user()->id,
            'following_userId'=> $validatedData["following_userId"],
        ];
        //checking if a follower exists
        $follower = Follower::where('user_id', '=', Auth()->user()->id)
        ->where('following_userId', '=', $validatedData["following_userId"])
        ->first();

        if($follower)
        {
            return response()->json([
                "status" => "failure",
                "status_code" => StatusCodes::UNPROCESSABLE,
                "message" => "Following already exists"
            ],StatusCodes::UNPROCESSABLE);
        }
        // saving a following
        Follower::create($data);

        //fire an event to increase

        return response()->json([
            "status" => "success",
            "status_code" => StatusCodes::SUCCESS,
            "message" => "Following successful"
        ],StatusCodes::SUCCESS);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\follower  $follower
     * @return \Illuminate\Http\Response
     */
    public function show(follower $follower)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\follower  $follower
     * @return \Illuminate\Http\Response
     */
    public function edit(follower $follower)
    {
        //

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\follower  $follower
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, follower $follower)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\follower  $follower
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $user = User::findorFail($id);
        if(!$user)
        {
            return response()->json([
                "status" => "failure",
                "status_code" => StatusCodes::UNPROCESSABLE,
                "message" => "User does not exists"
            ],StatusCodes::UNPROCESSABLE);
        }

        $follower = Follower::where('user_id', '=', Auth()->user()->id)
        ->where('following_userId', '=', $id)
        ->first();

        if(!$follower)
        {
            return response()->json([
                "status" => "failure",
                "status_code" => StatusCodes::UNPROCESSABLE,
                "message" => "User not following"
            ],StatusCodes::UNPROCESSABLE);
        }


        Follower::destroy($follower->id);
        //reduce count
        
        return response()->json([
            "status" => "success",
            "status_code" => StatusCodes::SUCCESS,
            "message" => "Un-following successful"
        ],StatusCodes::SUCCESS);
    }
    
    public function following(){
        $followings = Follower::where('user_id', '=', Auth()->user()->id)->get();
        $result = $followings->map(function($follow){
            return [
                "id"=> $follow->following->id,
                "name"=> $follow->following->name,
                "username"=> $follow->following->username,
                "img_url"=> $follow->following->profile_image_url ,
            ];
        });

        return response()->json([
            "status" => "success",
            "status_code" => StatusCodes::SUCCESS,
            "message" => "all followings",
            "data" => $result
        ],StatusCodes::SUCCESS);
    }

    public function followers(){
        $followers= Follower::where('following_userId', '=', Auth()->user()->id)->get();
        $result = $followers->map(function($follower){
            return [
                "id"=> $follower->user->id,
                "name"=> $follower->user->name,
                "username"=> $follower->user->username,
                "img_url"=> $follower->user->profile_image_url ,
            ];
        });

        return response()->json([
            "status" => "success",
            "status_code" => StatusCodes::SUCCESS,
            "message" => "all followers",
            "data" => $result
        ],StatusCodes::SUCCESS);
    }
}

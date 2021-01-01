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
    public $id_auth_user;
    public $id_other_user;
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

        $id_auth_user = Auth()->user()->id;
        $id_other_user = $validatedData["following_userId"];
        if($id_auth_user == $id_other_user){
            return response()->json([
                "status" => "failure",
                "status_code" => StatusCodes::UNPROCESSABLE,
                "message" => "You can't follow your profile"
            ],StatusCodes::UNPROCESSABLE);
        }
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
        $this->increaseFollowing($id_auth_user, $id_other_user );

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
        $id_other_user = $id;
        $id_auth_user =  Auth()->user()->id;

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
        $this->decreaseFollowing($id_auth_user, $id_other_user);
        

        return response()->json([
            "status" => "success",
            "status_code" => StatusCodes::SUCCESS,
            "message" => "Un-following successful"
        ],StatusCodes::SUCCESS);
    }
    
    public function following(){
        $followings = Follower::select('following_userId')->where('user_id', '=', Auth()->user()->id)->with('following')->get();

        return response()->json([
            "status" => "success",
            "status_code" => StatusCodes::SUCCESS,
            "message" => "all followings",
            "data" => $followings
        ],StatusCodes::SUCCESS);
    }

    public function followers(){
        $followers= Follower::select('user_id')->where('following_userId', '=', Auth()->user()->id)->with('user')->get();

        return response()->json([
            "status" => "success",
            "status_code" => StatusCodes::SUCCESS,
            "message" => "all followers",
            "data" => $followers
        ],StatusCodes::SUCCESS);
    }

    public function increaseFollowing($id_auth_user, $id_other_user)
    {
        // $this->id_auth_user, $this->id_other_user

        $user = User::find($id_other_user);
        $user->followerCount = $user->followerCount  + 1;
        $user->save();

        $newuser = User::find($id_auth_user);
        $newuser->followingCount = $user->followingCount  + 1;
        $newuser->save();
    }

    public function decreaseFollowing($id_auth_user, $id_other_user)
    {
        // $this->id_auth_user, $this->id_other_user
        $user = User::find($id_other_user);
        $user->followerCount = $user->followerCount  - 1;
        $user->save();

        $user = User::find($id_auth_user);
        $user->followingCount = $user->followingCount  - 1;
        $user->save();
    }
}

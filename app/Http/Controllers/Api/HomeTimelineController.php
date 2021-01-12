<?php

namespace App\Http\Controllers\Api;

use App\Collections\StatusCodes;
use App\Http\Controllers\Controller;
use App\Models\Follower;
use App\Models\Post;
use App\User;
use Illuminate\Http\Request;

class HomeTimelineController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $user = Auth()->user();
        if ($user->followingCount == 0) {

            $userIdArray = $this->getAllUsersIdWithSamePreference();

            $allPost = $this->getLikeUsersPost($userIdArray);

            return response()->json([
                "status" => "success",
                "message" => "You are not following anyone.",
                "data" => $allPost->load('user'),
            ], StatusCodes::SUCCESS);
        }

        $userIdArray = $this->getFollowing();

        $allPost = $this->getLikeUsersPost($userIdArray);

        return response()->json([
            "status" => "success",
            "message" => "Home Timeline Retrieved Successfully.",
            "data" => $allPost->load('user')
        ], StatusCodes::SUCCESS);
    }

    private function getAllUsersIdWithSamePreference()
    {
        $user = Auth()->user();

        $usersWithSamePreference = User::where('preference_id', $user->preference_id)->select('id')->get();

        $allUsersId = array();

        foreach ($usersWithSamePreference as $usersId) {
            array_push($allUsersId, $usersId->id);
        }
        return $allUsersId;
    }

    private function getLikeUsersPost($array)
    {
        $allPost = Post::whereIn('user_id', $array)
            ->with(['Comment' => function ($query) {
                return $query->with('user');
            }])->with(['likes' => function ($query) {
                return $query->where('liked', 1)->with('user');
            }])->latest()->get();

        return $allPost;
    }

    private function getFollowing()
    {

        $id = Auth()->user()->id;

        $followingUsersID = Follower::where('user_id', $id)->select('following_userId')->get();

        $allUsersId = array($id);

        foreach ($followingUsersID as $usersId) {
            array_push($allUsersId, $usersId->following_userId);
        }

        return $allUsersId;
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
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        //
    }
}

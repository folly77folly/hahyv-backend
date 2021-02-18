<?php

namespace App\Http\Controllers\Api;

use App\User;
use App\Models\Post;
use App\Models\Follower;
use Illuminate\Http\Request;
use App\Collections\Constants;
use Illuminate\Support\Carbon;
use App\Collections\StatusCodes;
use App\Http\Controllers\Controller;

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

            $allPosts = $this->getLikeUsersPost($userIdArray);

            return response()->json([
                "status" => "success",
                "message" => "You are not following anyone.",
                "data" => [],
            ], StatusCodes::SUCCESS);
        }else{

            $userIdArray = $this->getFollowing();
        
            $allPosts = $this->getLikeUsersPost($userIdArray);
            return response()->json([
                "status" => "success",
                "message" => "Home Timeline Retrieved Successfully.",
                "data" => $allPosts
            ], StatusCodes::SUCCESS);
        }


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
            }])->with(['user' => function($query){
                
                return $query->with(['subscribers' => function($query){
                    return $query->where('is_active', true);
                }])->with([
                    'monetizeBenefits:user_id,benefits', 
                    'subscriptionBenefits:user_id,benefits', 
                    ])->with([
                        'subscriptionRates' => function($query){
                            $query->with('subscription:id,name,period');
                        }]);
            }])->with(['polls'=>function($query){
                return $query->with('votes');
            }])
            ->latest()->Paginate(Constants::PAGE_LIMIT);
        
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

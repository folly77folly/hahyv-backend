<?php

namespace App\Http\Controllers\API;

use App\User;
use App\Models\Follower;
use Illuminate\Http\Request;
use App\Collections\StatusCodes;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class UserProfileController extends Controller
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
    public function profile(int $id)
    {

        $user = User::Where('id', $id)
            ->select(
                'name',
                'username',
                'email',
                'description',
                'preference_id',
                'profile_image_url',
                'website_url',
                'gender',
                'date_of_birth',
                'is_active',
                'is_reported',
                'is_blocked',
                'followerCount',
                'followingCount',
                'fansCount',
                'postCount',
                'walletBalance',
                'tokenBalance',
                'subscription_plan',
                'is_monetize',
                'subscription_amount',
                'cover_image_url',
                'location',
                'created_at',
                'updated_at'
            )
            ->first();
        // $user = User::Where('id', $id)->first();

        if (!$user) {
            return response()->json([
                "status" => "Not found",
                "message" => "This user does not exist in the Database",
            ], StatusCodes::NOT_FOUND);
        }

        return response()->json([
            "status" => "success",
            "message" => "Profile retrieved Successful",
            "data" => $user
        ], StatusCodes::SUCCESS);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $id)
    {
        $request->validate(
            [
                'email' => 'email'
            ],
            [
                'email.email' => 'The email address is not correct'
            ]
        );

        $user = User::Where('id', $id)->first();

        if (!$user) {
            return response()->json([
                "status" => "Not found",
                "message" => "This user does not exist in the Database",
            ], StatusCodes::NOT_FOUND);
        }

        DB::table('users')->where('id', $id)->update($request->all());

        $user = User::Where('id', $id)
            ->select(
                'name',
                'username',
                'email',
                'description',
                'preference_id',
                'profile_image_url',
                'website_url',
                'gender',
                'date_of_birth',
                'is_active',
                'is_reported',
                'is_blocked',
                'followerCount',
                'followingCount',
                'fansCount',
                'postCount',
                'walletBalance',
                'tokenBalance',
                'subscription_plan',
                'is_monetize',
                'subscription_amount',
                'cover_image_url',
                'location'
            )->first();


        return response()->json([
            "status" => "success",
            "message" => "Profile updated Successful",
            "data" => $user
        ], StatusCodes::SUCCESS);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        $user = User::Where('id', $id)->first();

        if (!$user) {
            return response()->json([
                "status" => "Not found",
                "message" => "This user does not exist in the Database",
            ], StatusCodes::NOT_FOUND);
        }

        $user->delete();

        return response()->json([
            "status" => "success",
            "message" => "User Deleted Successful"
        ], StatusCodes::SUCCESS);
    }

    public function preference()
    {   
        $userP = Auth()->user()->preference_id;
        $id = Auth()->user()->id;
        
        //array of following
        $my_followers= [];
        $followings = Follower::where('user_id', '=', Auth()->user()->id)->get(['following_userId AS id'])->toArray();
        foreach($followings as $following){
            array_push($my_followers, $following['id']);
        }

        if ($userP != 1){
            $suggestions = User::all(
                'id',
                'name',
                'username',
                'email',
                'description',
                'profile_image_url',
                'cover_image_url',
                'preference_id',
                'created_at',
                'updated_at'
            )
            ->where('preference_id', '==', $userP)
            ->where('id', '!=', $id )
            ->whereNotIn('id', $my_followers)
            ->toArray();

            return response()->json([
                "status"=>"success",
                "status_code" => StatusCodes::SUCCESS,
                "message" => "suggestions successfully fetched",
                "data" => array_values($suggestions)
            ]);
        }

        $suggestions = User::all(
            'id',
            'name',
            'username',
            'email',
            'description',
            'profile_image_url',
            'cover_image_url',
            'preference_id',
            'created_at',
            'updated_at'
        )
        ->where('id', '!=', $id )
        ->toArray();

        return response()->json([
            "status"=>"success",
            "status_code" => StatusCodes::SUCCESS,
            "message" => "suggestions successfully fetched",
            "data" => array_values($suggestions)
        ]);

    }
}

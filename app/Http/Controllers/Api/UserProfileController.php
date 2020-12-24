<?php

namespace App\Http\Controllers\API;

use App\Collections\StatusCodes;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;

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
    public function profile($id)
    {

        $user = User::Where('id', $id)
            ->select(
                'name',
                'username',
                'email',
                'description',
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
                'cover_image_url'
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
    public function update(Request $request, $id)
    {
        $request->validate(
            [
                'email' => 'email'
            ],
            [
                'email.email' => 'The email address is not correct'
            ]
        );

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
                'cover_image_url'
            )->first();

        if (!$user) {
            return response()->json([
                "status" => "Not found",
                "message" => "This user does not exist in the Database",
            ], StatusCodes::NOT_FOUND);
        }

        $user->update($request->all());

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
    public function destroy($id)
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
}

<?php

namespace App\Http\Controllers\Api\Admin;

use App\User;
use App\Jobs\PayoutJob;
use App\Jobs\PayoutJobUser;
use Illuminate\Http\Request;
use App\Collections\StatusCodes;
use App\Http\Controllers\Controller;
use App\Http\Requests\PayoutRequest;

class PayoutController extends Controller
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
        dispatch(new PayoutJob())->delay(now()->addMinutes(5));

        return response()->json([
            "status" => "success",
            "status_code" => StatusCodes::SUCCESS,
            "message" => "payout process started...",
        ],StatusCodes::SUCCESS,);
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

    public function payoutUser(PayoutRequest $request)
    {
        //
        $validatedData = $request->validated();

        $id = $validatedData['user_id'];
        $user = User::find($id);
        if ($user->availableEarning <= 0){
            return response()->json([
                "status" => "failure",
                "status_code" => StatusCodes::BAD_REQUEST,
                "message" => "No earnings recorded for this user",
            ],StatusCodes::BAD_REQUEST,);
        }

        dispatch(new PayoutJobUser($id));

        return response()->json([
            "status" => "success",
            "status_code" => StatusCodes::SUCCESS,
            "message" => "payout process started...",
        ],StatusCodes::SUCCESS,);
    }
}

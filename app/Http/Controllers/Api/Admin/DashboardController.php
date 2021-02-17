<?php

namespace App\Http\Controllers\Api\Admin;

use App\User;
use Illuminate\Http\Request;
use App\Collections\Constants;
use App\Mail\AnnouncementMail;
use App\Collections\StatusCodes;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\AnnouncementRequest;
use App\Http\Requests\DeactivateUserRequest;

class DashboardController extends Controller
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
    public function allUsers(){
        $users  = User::where('role_id', '!=', 1)->latest()->paginate(Constants::PAGE_LIMIT);
        return response()->json([
            "status" => "success",
            "status_code" => StatusCodes::SUCCESS,
            "message" => "all users retrieved",
            "data" => $users
        ],StatusCodes::SUCCESS,);
    }

    public function deactivateUser(DeactivateUserRequest $request){
        $validatedData = $request->validated();
        $user  = User::find($validatedData['user_id']);
        if($user->is_active == 1){
            $user->is_active = 0;
        }else{
            $user->is_active = 1;
        }
        $user->save();
        return response()->json([
            "status" => "success",
            "status_code" => StatusCodes::SUCCESS,
            "message" => "update successfully",
            "data" => $user
        ],StatusCodes::SUCCESS,);
    }

    public function sendMail(AnnouncementRequest $request){
        $mailDetails = $request->validated();
        $user  = User::find($mailDetails['user_id']);
        $mailDetails['name'] =$user->name;
        $email = $user->email;
        Mail::to($email)->send(new AnnouncementMail($mailDetails));
        return response()->json([
            "status" => "success",
            "status_code" => StatusCodes::SUCCESS,
            "message" => "update successfully",
            "data" => $user
        ],StatusCodes::SUCCESS,);
    }
}

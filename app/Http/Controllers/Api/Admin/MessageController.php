<?php

namespace App\Http\Controllers\Api\Admin;

use App\User;

use Illuminate\Http\Request;
use App\Traits\AdminJobsTrait;
use App\Collections\StatusCodes;
use App\Http\Controllers\Controller;
use App\Http\Requests\AnnouncementRequest;
use App\Http\Requests\MessageToUserRequest;

class MessageController extends Controller
{
    use AdminJobsTrait;
    //
    public function user(MessageToUserRequest $request){
        $mailDetails = $request->validated();
        $users  = User::where('id', $mailDetails['user_id'])->pluck('email','username');

        $this->dispatchJob($mailDetails, $users);

        return response()->json([
            "status" => "success",
            "status_code" => StatusCodes::SUCCESS,
            "message" => "mail sent successfully",
        ],StatusCodes::SUCCESS,);
    }

    public function users(AnnouncementRequest $request){
        $mailDetails = $request->validated();
        $users  = User::where('role_id', 2)->pluck('email','username');

        $this->dispatchJob($mailDetails, $users);

        return response()->json([
            "status" => "success",
            "status_code" => StatusCodes::SUCCESS,
            "message" => "mail sent successfully",
        ],StatusCodes::SUCCESS,);
    }

    public function subscribers(AnnouncementRequest $request){
        $mailDetails = $request->validated();
        $users  = User::where('is_monetize', false)->pluck('email','username');
        $this->dispatchJob($mailDetails, $users);

        return response()->json([
            "status" => "success",
            "status_code" => StatusCodes::SUCCESS,
            "message" => "mail sent successfully",
        ],StatusCodes::SUCCESS,);
    }

    public function creators(AnnouncementRequest $request){
        $mailDetails = $request->validated();
        $users  = User::where('is_monetize', true)->pluck('email','username');
        $this->dispatchJob($mailDetails, $users);

        return response()->json([
            "status" => "success",
            "status_code" => StatusCodes::SUCCESS,
            "message" => "mail sent successfully",
        ],StatusCodes::SUCCESS,);
    }
}

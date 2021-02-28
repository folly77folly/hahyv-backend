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
        $users  = User::where('id', $mailDetails['user_id'])->where('email_verified_at', '!=', null)->pluck('email','username');

        if ($users){
            $this->dispatchJob($mailDetails, $users)->delay(now()->addMinutes(5));

            return response()->json([
                "status" => "success",
                "status_code" => StatusCodes::SUCCESS,
                "message" => "mail sent successfully",
            ],StatusCodes::SUCCESS,);
        }else{
            return response()->json([
                "status" => "failure",
                "status_code" => StatusCodes::BAD_REQUEST,
                "message" => "user not verified",
            ],StatusCodes::BAD_REQUEST);
        }

    }

    public function users(AnnouncementRequest $request){
        $mailDetails = $request->validated();
        $users  = User::where('role_id', 2)->where('email_verified_at', '!=', null)->pluck('email','username');

        $this->dispatchJob($mailDetails, $users)->delay(now()->addMinutes(2));

        return response()->json([
            "status" => "success",
            "status_code" => StatusCodes::SUCCESS,
            "message" => "mail sent successfully",
        ],StatusCodes::SUCCESS);
    }

    public function subscribers(AnnouncementRequest $request){
        $mailDetails = $request->validated();
        $users  = User::where('is_monetize', false)->pluck('email','username');
        $this->dispatchJob($mailDetails, $users)->delay(now()->addMinutes(5));

        return response()->json([
            "status" => "success",
            "status_code" => StatusCodes::SUCCESS,
            "message" => "mail sent successfully",
        ],StatusCodes::SUCCESS,);
    }

    public function creators(AnnouncementRequest $request){
        $mailDetails = $request->validated();
        $users  = User::where('is_monetize', true)->pluck('email','username');
        $this->dispatchJob($mailDetails, $users)->delay(now()->addMinutes(5));

        return response()->json([
            "status" => "success",
            "status_code" => StatusCodes::SUCCESS,
            "message" => "mail sent successfully",
        ],StatusCodes::SUCCESS,);
    }
}

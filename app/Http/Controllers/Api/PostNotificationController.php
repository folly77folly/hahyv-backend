<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\PostNotification;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Events\PostNotificationEvent;
use App\Collections\StatusCodes;

class PostNotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $id = Auth()->user()->id;
        try{
            $postNotification = PostNotification::where('user_id', $id)->where('read', false)->with(['user', 'post'])->get();
            return response()->json([
                "status" =>"success",
                "status_code" =>StatusCodes::SUCCESS,
                "message" =>"notifications retrieved",
                "data" => $postNotification
                ],StatusCodes::SUCCESS);

        }catch(Exception $e){
            $commonFunction = new CommonFunctionsController;
            $array_json_return =$commonFunction->api_default_fail_response(__function__, $e);
            return response()->json($array_json_return, StatusCodes::BAD_REQUEST);
        }
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
    public function store(Array $data)
    {
        //

        try{
            $existing_notification = PostNotification::where([
                'message'=> $data['message'],
                'post_id' => $data['post_id'],
                'user_id' => $data['user_id']
            ])->first();
            // print_r($existing_notification);
            if(!$existing_notification){
                $new_postNotification = PostNotification::create($data);
                $notification = $new_postNotification->with(['user', 'post'])->get();
                broadcast(new PostNotificationEvent($notification[0]))->toOthers();
            }else{
                $notification = $existing_notification->with(['user', 'post'])->get();
                broadcast(new PostNotificationEvent($notification[0]))->toOthers();
            }
        }catch(Exception $e){
            return $e;
        }
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
        try{

            $notification = PostNotification::find($id);
            if (!$notification){
                return response()->json([
                    "status" =>"failure",
                    "status_code" =>StatusCodes::BAD_REQUEST,
                    "message" =>"notification not found",
                    ],StatusCodes::BAD_REQUEST); 
            }
            $notification->read = 0;
            $notification->update();
            return response()->json([
                "status" =>"success",
                "status_code" =>StatusCodes::SUCCESS,
                "message" =>"notification read",
                "data" => $notification
                ],StatusCodes::SUCCESS);
        }catch(Exception $e){
            $commonFunction = new CommonFunctionsController;
            $array_json_return =$commonFunction->api_default_fail_response(__function__, $e);
            return response()->json($array_json_return, StatusCodes::BAD_REQUEST);
        }
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Models\Bookmark;
use Illuminate\Http\Request;
use App\Collections\StatusCodes;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\BookmarkRequest;
use App\Http\Controllers\Api\CommonFunctionsController;

class BookmarkController extends Controller
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
            $bookmark = Bookmark::where('user_id', $id)->with(['user', 'post'])->get();
            return response()->json([
                "status" =>"success",
                "status_code" =>StatusCodes::SUCCESS,
                "message" =>"bookmarks retrieved",
                "data" => $bookmark
                ],StatusCodes::SUCCESS);
        }catch(\Exception  $e){
            $commonFunction = new CommonFunctionsController;
            $array_json_return =$commonFunction->api_default_fail_response(__function__, $e);
            return response()->json(array_json_return);
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
    public function store(BookmarkRequest $request)
    {
        //
        $id = Auth()->user()->id;
        
        $validatedData = $request->validated();
        // print_r ($validatedData);
        try{
            $bookmark = Bookmark::where('user_id', $id)->where('post_id', $validatedData['post_id'])->first();
            // echo $bookmark->id;
            if($bookmark){
                //record exists
                $existing_bookmark = Bookmark::find($bookmark->id);
                if($bookmark->status == 1){
                    $existing_bookmark->status = 0;
                    $existing_bookmark->save();
                    $result = $existing_bookmark->load(['user','post']);
                    return response()->json([
                        "status" =>"success",
                        "status_code" =>StatusCodes::SUCCESS,
                        "message" =>"bookmark removed",
                        "data" =>$result
                    ],StatusCodes::SUCCESS);
                }else{
                    $existing_bookmark->status = 1;
                    $existing_bookmark->save();
                    $result = $existing_bookmark->load(['user','post']);
                    return response()->json([
                        "status" =>"success",
                        "status_code" =>StatusCodes::SUCCESS,
                        "message" =>"bookmark added",
                        "data" =>$result
                    ],StatusCodes::SUCCESS);
                }

            }
            $data = [
                'user_id' => $id,
                'post_id' => $validatedData['post_id']
            ];
            $new_bookmark = Bookmark::create($data);
            $result = Bookmark::find($new_bookmark->id)->load(['user', 'post']);

            return response()->json([
                "status" =>"success",
                "status_code" =>StatusCodes::SUCCESS,
                "message" =>"bookmark added",
                "data" =>$result
            ],StatusCodes::SUCCESS);
        }catch(Exception $e){
            $commonFunction = new CommonFunctionsController;
            $array_json_return =$commonFunction->api_default_fail_response(__function__, $e);
            return response()->json(array_json_return);
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
    }
}

<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\Post;
use App\Models\Bookmark;
use Illuminate\Http\Request;
use App\Collections\Constants;
use App\Collections\StatusCodes;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\BookmarkRequest;
use App\Http\Controllers\Api\CommonFunctionsController;
use App\Http\Controllers\Api\PostNotificationController;


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
            $bookmark = Bookmark::where('user_id', $id)->where('status', true)->with(['post'=>function($query){
                $query->with(['user', 'comment'=>function($query_comment){
                    $query_comment->with('user');
                }, 'likes'=>function($query_likes){
                    $query_likes->with('user');
                }]);
            }])->paginate(Constants::PAGE_LIMIT);
            return response()->json([
                "status" =>"success",
                "status_code" =>StatusCodes::SUCCESS,
                "message" =>"bookmarks retrieved",
                "data" => $bookmark
                ],StatusCodes::SUCCESS);
        }catch(\Exception  $e){
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
    public function store(BookmarkRequest $request)
    {
        //
        $id = Auth()->user()->id;
        $username = Auth()->user()->username;

        $validatedData = $request->validated();
        // print_r ($validatedData);
        try{
            $bookmark = Bookmark::where('user_id', $id)->where('post_id', $validatedData['post_id'])->first();
            if($bookmark){
                //record exists
                $existing_bookmark = Bookmark::find($bookmark->id);
                if($bookmark->status == 1){
                    $existing_bookmark->status = 0;
                    $existing_bookmark->save();
                    $result = $existing_bookmark->load(['post' => function($query){
                        $query->with(['user', 'comment'=>function($query_comment){
                            $query_comment->with('user');
                        }, 'likes'=>function($query_likes){
                            $query_likes->with('user');
                        }]);
                    }]);
                    return response()->json([
                        "status" =>"success",
                        "status_code" =>StatusCodes::SUCCESS,
                        "message" =>"bookmark removed",
                        "data" =>$result
                    ],StatusCodes::SUCCESS);
                }else{
                    $existing_bookmark->status = 1;
                    $existing_bookmark->save();
                    $result = $existing_bookmark->load(['post' => function($query){
                        $query->with(['user', 'comment'=>function($query_comment){
                            $query_comment->with('user');
                        }, 'likes'=>function($query_likes){
                            $query_likes->with('user');
                        }]);
                    }]);
                    // Notify the owner of the post

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
            $result = Bookmark::find($new_bookmark->id)->load(['post' => function($query){
                $query->with(['user', 'comment'=>function($query_comment){
                    $query_comment->with('user');
                }, 'likes'=>function($query_likes){
                    $query_likes->with('user');
                }]);
            }]);
            
            //Notify Owner of Post
            $post = Post::find($request->post_id);
            $this->notify($post, $username, 'bookmark');

            return response()->json([
                "status" =>"success",
                "status_code" =>StatusCodes::SUCCESS,
                "message" =>"bookmark added",
                "data" =>$result
            ],StatusCodes::SUCCESS);
        }catch(Exception $e){
            $commonFunction = new CommonFunctionsController;
            $array_json_return =$commonFunction->api_default_fail_response(__function__, $e);
            return response()->json($array_json_return, StatusCodes::BAD_REQUEST);
        }
 

    }

    public function notify($post, $username, $type){
        $post_notify = new PostNotificationController();
        $result = $post_notify->store([
            'message'=> "$username $type your post",
            'post_id' => $post->id,
            'user_id' => Auth()->user()->id,
            'broadcast_id' => $post->user->id,
            'post_type_id' => Constants::NOTIFICATION['BOOKMARK'],
        ]);
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

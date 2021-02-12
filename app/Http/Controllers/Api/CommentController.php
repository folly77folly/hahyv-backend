<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\Comment;
use Illuminate\Http\Request;
use App\Collections\StatusCodes;
use App\Http\Controllers\Controller;
use App\Http\Requests\CommentRequest;
use Symfony\Component\HttpKernel\Exception\HttpException;

class CommentController extends Controller
{
    
    public function __construct(){
        $this->middleware('comment', ['only'=>['store']]);
        $this->middleware('comment_like', ['only'=>['store']]);
    }
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
    public function store(CommentRequest $request)
    {
        //
        $id = Auth()->user()->id;
        $validatedData = $request->validated();

        
        $image = null;
        $video = null;

        if(isset($validatedData['image'])) {
            $image = $validatedData['image'];
        }
        if(isset($validatedData['video'])) {
            $video = $validatedData['video'];
        }

        $data = [   
            'user_id' => $id,
            'post_id' => $validatedData['post_id'],
            'comment' => $validatedData['comment'],
            'picture' => $image,
            'video' => $video
        ];


        try{

            $comment = Comment::create($data);
            $result  = [
                "comment_id" => $comment["id"],
                "user_id" => $comment["user_id"],
                "post_id" => $comment["post_id"],
                "comment" => $comment["comment"],
                "image" => $image,
                "video" => $video,
                "created_at" => $comment["created_at"],
                "updated_at" => $comment["updated_at"]
            ];
            return response()->json([
                "status" => "success",
                "status_code" => StatusCodes::SUCCESS,
                "message" => "Comment posted successfully",
                "data" => $result
            ], StatusCodes::SUCCESS);
        }catch(Exception $e){
            return response()->json([
                "status" => "failure",
                "message" => $e.message
            ],StatusCode::BAD_REQUEST);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function show(Comment $comment)
    {
        //

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function edit(Comment $comment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Comment $comment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $comment = Comment::find($id);
        if ($comment->user_id != Auth()->user()->id)
        {
            return response()->json([
                "status" => "success",
                "status_code" => StatusCodes::BAD_REQUEST,
                "message" => "This Comment was not posted by you"
            ], StatusCodes::BAD_REQUEST);
        }
        try{

            $comment->delete();
            return response()->json([
                "status" => "success",
                "status_code" => StatusCodes::SUCCESS,
                "message" => "Comment deleted successfully"
            ], StatusCodes::SUCCESS);

        }catch(Exception $e ){

            return response()->json([
                "status" => "success",
                "status_code" => StatusCodes::BAD_REQUEST,
                "message" => $e.message
            ], StatusCodes::BAD_REQUEST);
        }
    }
}

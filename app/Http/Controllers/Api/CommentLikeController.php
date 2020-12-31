<?php

namespace App\Http\Controllers\Api;
use App\Models\CommentLike;
use Illuminate\Http\Request;
use App\Collections\StatusCodes;
use App\Http\Controllers\Controller;
use App\Http\Requests\CommentLikeRequest;

class CommentLikeController extends Controller
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
    public function store(CommentLikeRequest $request)
    {
        //
        $id = Auth()->user()->id;
        $validatedData = $request->validated();

        //find if a like record exists and Update it
        $likedComments = CommentLike::where([
            'user_id'=> $id,
            'comment_id'=> $validatedData['comment_id']
            ])->first();

        if ($likedComments){
            if($likedComments->liked == 1){
                $data = ['liked'=> 0];
                $likedComments->update($data);
                return response()->json([
                    "status" => "success",
                    "status_code" => StatusCodes::SUCCESS,
                    "message" => "Comment unliked successfully",
                    "data" => $likedComments
                ], StatusCodes::SUCCESS);

            }else{
                $data = ['liked'=> 1];
                $likedComments->update($data);
                return response()->json([
                    "status" => "success",
                    "status_code" => StatusCodes::SUCCESS,
                    "message" => "Comment liked successfully",
                    "data" => $likedComments
                ], StatusCodes::SUCCESS);
            }
        }

        $data = [   
            'user_id' => $id,
            'comment_id' => $validatedData['comment_id'],
        ];

        try{

            $commentLike = CommentLike::create($data);
            $results  = [
                "comment_like_id" => $commentLike->id,
                'user_id' => $id,
                'comment_id' => $commentLike->comment_id,
                'like' => $commentLike->liked,
            ];
            return response()->json([
                "status" => "success",
                "status_code" => StatusCodes::SUCCESS,
                "message" => "Comment liked successfully",
                "data" => CommentLike::find($commentLike->id)
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

    public function comment_unlike(CommentLikeRequest $request)
    {
        $id = Auth()->user()->id;
        $validatedData = $request->validated();

        $likedComment = CommentLike::where([
            'user_id'=> $id,
            'comment_id' => $validatedData['comment_id'],
            ])->first();

        if(!$likedComment){
            return response()->json([
                "status" => "failure",
                "message" => $e.message
            ],StatusCode::BAD_REQUEST);
        }

        // $data = [   
        //     'user_id' => $id,
        //     'comment_id' => $validatedData['comment_id'],
        // ];


        $likedComment->liked = 0;
        $likedComment->save();
            return response()->json([
                "status" => "success",
                "status_code" => StatusCodes::SUCCESS,
                "message" => "Comment unliked successfully",
            ], StatusCodes::SUCCESS);
    }
}

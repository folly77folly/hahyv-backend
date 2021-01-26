<?php

namespace App\Http\Controllers\Api;

use App\Models\Poll;
use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PollController extends Controller
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
        $request->validate([
            'description' => 'required_if:images,==,[]',
        ]);
        
        $id = Auth()->user()->id;
        $post = new Post;

        $post->description = $request->input('description');
        $post->images = $request->input('images');
        $post->videos = $request->input('videos');
        $post->user_id = $id;
        $post->poll = $request->input('poll');

        if($request->input('height') != null ){
            $post->height = $request->input('height');
        }
        if($request->input('width') != null ){
            $post->width = $request->input('width');
        }
        if($request->input('orientation') != null ){
            $post->orientation = $request->input('orientation');
        }

        $post->save();
        $data =[];
        foreach ($choices as $choice){
            $the_choice = [
                'post_id' => $post->id,
                'choice' => $choice
            ];
            array_push($data, $choice);
        }
        $post->polls()->createMany($data);
        // insert into poll
        return response()->json([
            "status" => "success",
            "message" => "Post created successfully.",
            "data" => ''
        ], StatusCodes::CREATED);
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

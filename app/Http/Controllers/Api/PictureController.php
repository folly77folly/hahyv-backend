<?php

namespace App\Http\Controllers\Api;

use App\Models\Picture;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Collections\StatusCodes;
use App\Http\Controllers\Controller;
use App\Http\Requests\PictureRequest;

class PictureController extends Controller
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
        $photos = Picture::where('user_id', '=', $id )->latest()->get();

        $result  = $photos->map(function($photo){
            return [
                "user_id" => $photo->user_id,
                "title" => $photo->title,
                "photos" => unserialize($photo->photos)
            ];
        });

        return response()->json([
            "status" => "success",
            "status_code" => StatusCodes::SUCCESS,
            "message" => "Records fetched successfully",
            "data" => $result
        ],StatusCodes::SUCCESS);
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
    public function store(PictureRequest $request)
    {
        //
        $id = Auth()->user()->id;
        $validatedData = $request->validated();
        $data = [
            "user_id" => $id,
            "title" => $validatedData["title"],
            "photos" => serialize($validatedData["photos"]),
            "photoCount" => count($validatedData["photos"]),
            "photoTag" => Str::slug($validatedData["title"])
        ];

        Picture::create($data);

        return response()->json([
            "status" => "success",
            "status_code" => StatusCodes::SUCCESS,
            "message" => "Photos uploaded successfully",
            "data" => array(
                "user_id" => $data["user_id"],
                "title" => $data["title"],
                "photos" => unserialize($data["photos"])
            )
        ],StatusCodes::SUCCESS);
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

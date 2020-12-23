<?php

namespace App\Http\Controllers\API;

use App\Collections\StatusCodes;
use App\Models\Preference;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PreferenceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $preferences = Preference::select('id','preference')->get();

        return response()->json([
            "status" => "success",
            "message" => "Preferences retrieved successfully.",
            "data" => $preferences
        ], StatusCodes::SUCCESS);
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
        $preference = New Preference;

        $preference->preference = $request->input('preference');

        $preference->save();

        return response()->json([
            "status" => "success",
            "message" => "Preferences created successfully.",
            "data" => $preference
        ], StatusCodes::CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\preference  $preference
     * @return \Illuminate\Http\Response
     */
    public function show(preference $preference)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\preference  $preference
     * @return \Illuminate\Http\Response
     */
    public function edit(preference $preference)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\preference  $preference
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate(
            [
                'preference' => 'required'
            ],
            [
                'preference.unique' => 'This preference already exist'
            ]
        );
        $preference = Preference::find($id);
        $preference->update(['preference' => $request->preference]);

        return response()->json([
            "status" => "success",
            "message" => "Preferences updated successfully.",
            "data" => $preference
        ], StatusCodes::SUCCESS);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\preference  $preference
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $preference = Preference::Where('id', $id)->first();

        if (!$preference) {
            return response()->json([
                "status" => "Not found",
                "message" => "This preference does not exist in the Database",
            ], StatusCodes::NOT_FOUND);
        }

        $preference->delete();

        return response()->json([
            "status" => "success",
            "message" => "preference Deleted Successful"
        ], StatusCodes::SUCCESS);
    }
}

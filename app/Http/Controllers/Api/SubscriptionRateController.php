<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Collections\StatusCodes;
use App\Models\SubscriptionRate;
use App\Http\Controllers\Controller;
use App\Http\Requests\SubscriptionRateRequest;

class SubscriptionRateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $subRate = SubscriptionRate::where('user_id', Auth()->user()->id)->with('subscription:id,name,period')->get();
        return response()->json([
            "status" => "success",
            "status_code" => StatusCodes::SUCCESS,
            "message" => "Subscription rates retrieved.",
            "data" => $subRate
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
    public function store(SubscriptionRateRequest $request)
    {
        //
        
        $validatedData = $request->validated();
        $validatedData['user_id'] = Auth()->user()->id;
        $subRate = SubscriptionRate::firstOrCreate($validatedData);
        return response()->json([
            "status" => "success",
            "status_code" => StatusCodes::SUCCESS,
            "message" => "Subscription rate added.",
            "data" => $subRate->load('subscription')
        ], StatusCodes::SUCCESS);
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
        SubscriptionRate::destroy($id);
        return response()->json([
            "status" => "success",
            "status_code" => StatusCodes::SUCCESS,
            "message" => "Subscription rate removed.",
        ], StatusCodes::SUCCESS);
    }
}

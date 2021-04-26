<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\MonetizeBenefit;
use App\Collections\StatusCodes;
use App\Http\Controllers\Controller;
use App\Http\Requests\MonetizeBenefitRequest;

class MonetizeBenefitController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $monetizeBenefit = MonetizeBenefit::where('user_id', Auth()->user()->id)->get();
        return response()->json([
            "status" => "success",
            "status_code" => StatusCodes::SUCCESS,
            "message" => "Monetize benefits retrieved.",
            "data" => $monetizeBenefit
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
    public function store(MonetizeBenefitRequest $request)
    {
        //
        $validatedData = $request->validated();
        $validatedData['user_id'] = Auth()->user()->id;
        $monetizeBenefit = MonetizeBenefit::firstOrCreate($validatedData);
        return response()->json([
            "status" => "success",
            "status_code" => StatusCodes::SUCCESS,
            "message" => "Monetize benefit added.",
            "data" => $monetizeBenefit
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
    public function update(MonetizeBenefitRequest $request, $id)
    {
        //
        $validatedData = $request->validated();

        $monetizeBenefit = MonetizeBenefit::find($id);
        $monetizeBenefit->benefits = $request->benefits;
        $monetizeBenefit->save();
        return response()->json([
            "status" => "success",
            "status_code" => StatusCodes::SUCCESS,
            "message" => "Monetize benefit updated.",
            "data" => $monetizeBenefit
        ], StatusCodes::SUCCESS);
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
        // $monetizeBenefit = MonetizeBenefit::find($id);
        
        // $monetizeBenefit->destroy();
        MonetizeBenefit::destroy($id);
        
        return response()->json([
            "status" => "success",
            "status_code" => StatusCodes::SUCCESS,
            "message" => "Monetize benefit deleted.",
        ], StatusCodes::SUCCESS);
    }
}

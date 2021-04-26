<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Collections\StatusCodes;
use App\Models\SubscriptionBenefit;
use App\Http\Controllers\Controller;
use App\Http\Requests\SubscriptionBenefitRequest;

class SubscriptionBenefitController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $subBenefit = SubscriptionBenefit::where('user_id', Auth()->user()->id)->get();
        return response()->json([
            "status" => "success",
            "status_code" => StatusCodes::SUCCESS,
            "message" => "Subscription benefits retrieved.",
            "data" => $subBenefit
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
    public function store(SubscriptionBenefitRequest $request)
    {
        //

        $validatedData = $request->validated();
        $validatedData['user_id'] = Auth()->user()->id;
        $subBenefit = SubscriptionBenefit::firstOrCreate($validatedData);
        return response()->json([
            "status" => "success",
            "status_code" => StatusCodes::SUCCESS,
            "message" => "Subscription benefit added.",
            "data" => $subBenefit
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
    public function update(SubscriptionBenefitRequest $request, $id)
    {
        //
        $validatedData = $request->validated();
        $subBenefit = SubscriptionBenefit::find($id);

        if(!$subBenefit){
            return response()->json([
                "status" => "success",
                "status_code" => StatusCodes::BAD_REQUEST,
                "message" => "Subscription not found.",
            ], StatusCodes::BAD_REQUEST);
        }
        $subBenefit->benefits = $validatedData['benefits'];

        $subBenefit->save();

        return response()->json([
            "status" => "success",
            "status_code" => StatusCodes::SUCCESS,
            "message" => "Subscription benefit updated.",
            "data" => $subBenefit
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
        // $subBenefit = SubscriptionBenefit::find($id);

        // $subBenefit->destroy();
        SubscriptionBenefit::destroy($id);
        return response()->json([
            "status" => "success",
            "status_code" => StatusCodes::SUCCESS,
            "message" => "Subscription benefit deleted.",
        ], StatusCodes::SUCCESS);
    }
}

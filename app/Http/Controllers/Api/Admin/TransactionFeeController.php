<?php

namespace App\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use App\Models\TransactionFee;
use App\Collections\StatusCodes;
use App\Http\Controllers\Controller;
use App\Http\Requests\TransactionFeeRequest;

class TransactionFeeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $transactionFee = TransactionFee::first();

        return response()->json([
            "status"=>"success",
            "status_code" => StatusCodes::SUCCESS,
            "message" => "transaction fee retrieved",
            "data" => $transactionFee
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
    public function store(TransactionFeeRequest $request)
    {
        //
        $validatedData = $request->validated();
        
        $transactionFee = TransactionFee::updateOrCreate([
            'id' => 1
        ], $validatedData);

        return response()->json([
            "status"=>"success",
            "status_code" => StatusCodes::SUCCESS,
            "message" => "transaction fee save successfully",
            "data" => $transactionFee
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

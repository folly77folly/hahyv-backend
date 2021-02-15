<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Collections\Constants;
use App\Collections\StatusCodes;
use App\Models\WithdrawalRequest;
use App\Http\Requests\WithRequest;
use App\Http\Controllers\Controller;

class WithdrawalRequestController extends Controller
{

    public function __construct(){
        $this->middleware('earning_balance', ['only' => ['store']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $withdrawals = WithdrawalRequest::where('user_id', Auth()->user()->id)->latest()->paginate(Constants::PAGE_LIMIT);
        return response()->json([
            "status" => "success",
            "status_code" => StatusCodes::SUCCESS,
            "message" => "withdrawals retrieved.",
            'response'=> $withdrawals
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
    public function store(WithRequest $request)
    {
        //
        $validatedData = $request->validated();
        $validatedData['user_id'] = Auth()->user()->id;
        $withdrawal = WithdrawalRequest::create($validatedData);
        return response()->json([
            "status" => "success",
            "status_code" => StatusCodes::SUCCESS,
            "message" => "Request successfully sent.",
            'response'=> $withdrawal
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
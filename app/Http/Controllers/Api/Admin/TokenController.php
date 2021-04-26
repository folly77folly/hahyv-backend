<?php

namespace App\Http\Controllers\Api\Admin;

use App\User;
use App\Models\TokenRate;
use Illuminate\Http\Request;
use App\Collections\StatusCodes;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Traits\TokenTransactionsTrait;
use App\Http\Requests\TokenRateRequest;
use App\Traits\WalletTransactionsTrait;

class TokenController extends Controller
{
    use WalletTransactionsTrait, TokenTransactionsTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $tokenRates = TokenRate::all();
        return response()->json([
            "status" => "success",
            "status_code" => StatusCodes::SUCCESS,
            "message" => "Token rate retrieved.",
            "data" => $tokenRates
        ], StatusCodes::SUCCESS);
    }

        /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TokenRateRequest  $request)
    {
        //
        $validateData = $request->validated();

        TokenRate::updateOrCreate(['id' => 1], $validateData);
        return response()->json([
            "status" => "success",
            "status_code" => StatusCodes::SUCCESS,
            "message" => "Token rate updated successfully.",
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

<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Collections\Constants;
use App\Collections\StatusCodes;
use App\Traits\CardPaymentTrait;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Traits\WalletTransactionsTrait;
use App\Http\Requests\FundWalletRequest;

class WalletController extends Controller
{
    use CardPaymentTrait, WalletTransactionsTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $transactions = WalletTransaction::where('user_id', Auth()->user()->id)->latest()->paginate(Constants::PAGE_LIMIT);
        return response()->json([
            "status" => "success",
            "status_code" => StatusCodes::SUCCESS,
            "message" => "transactions retrieved.",
            'response'=> $transactions
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
    public function store(Request $request)
    {
        //
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
    public function fundWallet(FundWalletRequest $request){
        $validatedData = $request->validated();
        $id = $validatedData['card_id'];
        $amount = $validatedData['amount'];
        $description = 'funded your wallet with '. $request->amount;
        $validatedData['description'] = $description;
        $stripe = new \Stripe\StripeClient("sk_test_51I8w9TFSTnpmya5shzFVTfBqBl5hefx32VScM5aZJfSOysNrnhMF9qtcKtnywOqbvHpRd5F6ZprE04wmYll0Cxyl00hzXI0HH5");
        $response = $this->chargeCard($validatedData, $stripe);

        if ($response['code'] == 0){
            $commonFunction = new CommonFunctionsController;
            $array_json_return =$commonFunction->api_default_fail_response(__function__, $response['result']);
            return response()->json($array_json_return, StatusCodes::BAD_REQUEST);
        }
        $this->creditWallet($amount, $description);
        return response()->json([
            "status" => "success",
            "status_code" => StatusCodes::SUCCESS,
            "message" => "Wallet funded successfully.",
            'response'=> $response['result']
        ],StatusCodes::SUCCESS);

    }
}

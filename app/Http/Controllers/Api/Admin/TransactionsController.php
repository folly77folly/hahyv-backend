<?php

namespace App\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use App\Collections\Constants;
use App\Models\CardTransaction;
use App\Collections\StatusCodes;
use App\Models\WalletTransaction;
use App\Models\EarningTransaction;
use App\Http\Controllers\Controller;
use App\Traits\WalletsApiPaymentTrait;
use App\Http\Requests\AdminTransactionsRequest;
use App\Http\Requests\AdminVerifyTransactionRequest;

class TransactionsController extends Controller
{
    use WalletsApiPaymentTrait;
    //
    public function earnings(AdminTransactionsRequest $request)
    {
        //
        $validatedData = $request->validated();
        $user_id = $request->user_id;
        $earning = EarningTransaction::where('user_id', $user_id)->with('type:id,name')->latest()->paginate(Constants::PAGE_LIMIT);
        return response()->json([
            "status" => "success",
            "status_code" => StatusCodes::SUCCESS,
            "message" => "Transactions retrieved .",
            "data" => $earning
        ], StatusCodes::SUCCESS);
    }

    public function wallet(AdminTransactionsRequest $request)
    {
        //
        $validatedData = $request->validated();
        $user_id = $request->user_id;
        $transactions = WalletTransaction::where('user_id', $user_id)->latest()->paginate(Constants::PAGE_LIMIT);
        return response()->json([
            "status" => "success",
            "status_code" => StatusCodes::SUCCESS,
            "message" => "transactions retrieved.",
            'response'=> $transactions
        ],StatusCodes::SUCCESS);
    }

    public function card(AdminTransactionsRequest $request)
    {
        $validatedData = $request->validated();
        $user_id = $request->user_id;
        $transactions = CardTransaction::where('user_id', $user_id)->latest()->paginate(Constants::PAGE_LIMIT);
        return response()->json([
            "status" =>"success",
            "status_code" =>StatusCodes::SUCCESS,
            "message" =>"transactions retrieved",
            "data" => $transactions
            ],StatusCodes::SUCCESS);
    }

    public function verifyTransaction(AdminVerifyTransactionRequest $request)
    {
        $validatedData = $request->validated();
        $refNo = $request->referenceNumber;
        $response = $this->checkBankTransferDetails($refNo);
        switch($response['status']){
            case true:
                return response()->json([
                    "status" =>"success",
                    "status_code" =>StatusCodes::SUCCESS,
                    "message" =>"retrieved",
                    "data" => $response['data']
                    ],StatusCodes::SUCCESS);
            case false:
                return response()->json([
                    "status" =>"failure",
                    "status_code" =>StatusCodes::BAD_REQUEST,
                    "message" =>"transactions failed",
                    "data" =>$response['data'],
                    ],StatusCodes::BAD_REQUEST);
            default:
                break;
        }
    }
}

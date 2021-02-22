<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Collections\Constants;
use App\Collections\StatusCodes;
use App\Models\WithdrawalRequest;
use App\Http\Requests\WithRequest;
use App\Http\Controllers\Controller;
use App\Traits\PayStackPaymentTrait;
use App\Traits\WalletTransactionsTrait;

class WithdrawalRequestController extends Controller
{
    use PayStackPaymentTrait, WalletTransactionsTrait;

    public function __construct(){
        $this->middleware('wallet_balance', ['only' => ['bankTransfer']]);
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

    public function bankTransfer(WithRequest $request){
        $validatedData = $request->validated();
        $description = "transfer to bank";
        $amount = $validatedData['amount'];

        if(Auth()->user()->walletBalance < $amount){
            return response()->json([
                "status" => "failure",
                "status_code" => StatusCodes::BAD_REQUEST,
                "message" => "you cannot withdraw more than your balance.",
            ],StatusCodes::BAD_REQUEST);
        }

        if(!Auth()->user()->bankDetail){
            return response()->json([
                "status" => "failure",
                "status_code" => StatusCodes::BAD_REQUEST,
                "message" => "bank details not found.",
            ],StatusCodes::BAD_REQUEST);
        }


        $fields = [
            'type' => "nuban",
            'name' => Auth()->user()->bankDetail->account_name,
            'account_number' => Auth()->user()->bankDetail->account_no,
            'bank_code' => Auth()->user()->bankDetail->bank_id,
            'currency' => "NGN"
          ];
        
          $getReceipt = $this->transferRecipient($fields);
        //   print_r($getReceipt);
          if (!$getReceipt){
                return response()->json([
                    "status" => "failure",
                    "status_code" => StatusCodes::BAD_REQUEST,
                    "message" => "Issuer not reachable try again later.",
                ],StatusCodes::BAD_REQUEST);
          }
          if (!$getReceipt->status){
            return response()->json([
                "status" => "failure",
                "status_code" => StatusCodes::BAD_REQUEST,
                "message" => $getReceipt->message,
            ],StatusCodes::BAD_REQUEST);
          }
          $recipient_code = $getReceipt->data->recipient_code;

          //Transfer Area
          $transferFields = [
            'source' => "balance",
            'amount' => $amount * 100,
            'recipient' => $recipient_code,
            'reason' => $description
          ];

          $response = $this->transfer($transferFields);

          if($response){
            // print_r($response->data->reference);
                if($response->status){

                    $this->debitWallet(Auth()->user()->id, $amount, $description, $response->data->reference, $response->data->transfer_code);

                    return response()->json([
                        "status" => "success",
                        "status_code" => StatusCodes::SUCCESS,
                        "message" => "withdrawal successfully sent.",
                        // 'response'=> $response
                    ],StatusCodes::SUCCESS);
                }else{
                    return response()->json([
                        "status" => "failure",
                        "status_code" => StatusCodes::BAD_REQUEST,
                        "message" => $response->message,
                    ],StatusCodes::BAD_REQUEST);
                }

          }else{
                return response()->json([
                    "status" => "failure",
                    "status_code" => StatusCodes::BAD_REQUEST,
                    "message" => "Host not reachable try again later.",
                ],StatusCodes::BAD_REQUEST);
          }
    }

}

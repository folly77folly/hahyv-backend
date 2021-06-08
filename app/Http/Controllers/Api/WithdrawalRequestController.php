<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Collections\Constants;
use App\Collections\StatusCodes;
use App\Mail\LowWalletFundEmail;
use App\Models\WithdrawalRequest;
use App\Http\Requests\WithRequest;
use App\Jobs\AdminLowWalletFundJob;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Traits\PayStackPaymentTrait;
use App\Traits\WalletsApiPaymentTrait;
use App\Traits\WalletTransactionsTrait;

class WithdrawalRequestController extends Controller
{
    use PayStackPaymentTrait, WalletTransactionsTrait, WalletsApiPaymentTrait;

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
                "message" => "You cannot withdraw more than your balance.",
            ],StatusCodes::BAD_REQUEST);
        }

        if(!Auth()->user()->bankDetail){
            return response()->json([
                "status" => "failure",
                "status_code" => StatusCodes::BAD_REQUEST,
                "message" => "Kindly add your bank details before transfer",
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

                    $this->debitWallet(Auth()->user()->id, $amount, $description, $response->data->reference, $response->data->transfer_code, 0);

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

    public function transfer(WithRequest $request)
    {
        $validatedData = $request->validated();
        $description = "transfer to bank";
        $amount = $validatedData['amount'];
        $transferFee = 26.3;
        $status = 0;

        if(Auth()->user()->walletBalance < $amount){
            return response()->json([
                "status" => "failure",
                "status_code" => StatusCodes::BAD_REQUEST,
                "message" => "You cannot withdraw more than your balance.",
            ],StatusCodes::BAD_REQUEST);
        }

        if(Auth()->user()->walletBalance < ($amount + $transferFee)){
            return response()->json([
                "status" => "failure",
                "status_code" => StatusCodes::BAD_REQUEST,
                "message" => "Your balance cannot accommodate transfer charges.",
            ],StatusCodes::BAD_REQUEST);
        }

        if(!Auth()->user()->bankDetail){
            return response()->json([
                "status" => "failure",
                "status_code" => StatusCodes::BAD_REQUEST,
                "message" => "Kindly add your bank details before transfer",
            ],StatusCodes::BAD_REQUEST);
        }

        $fields = [
            'BankCode' => Auth()->user()->bankDetail->bank_id,
            'AccountNumber' => Auth()->user()->bankDetail->account_no,
            'AccountName' => Auth()->user()->bankDetail->account_name,
            'TransactionReference' => rand(100000, 9999999),
            'Amount' => $amount,
            'Narration'=> $description,
            'SecretKey'=>env('Secret_Key', 'hfucj5jatq8h'),
        ];
        // dd($fields);
        $result = $this->doTransfer($fields);
        // dd($result);

        if($result['status']){

            $this->debitWallet(Auth()->user()->id, $amount, $description, $fields['TransactionReference'], $fields['TransactionReference'], $status);
            return response()->json([
                "status" => "success",
                "status_code" => StatusCodes::SUCCESS,
                "message" => "withdrawal is been processed.",
            ],StatusCodes::SUCCESS);
        }
        if ($result['message'] == "Your wallet balance is too low to complete this transaction. Please fund your wallet.")
        {
            //notify admin of low balance 
            dispatch(new AdminLowWalletFundJob());

            return response()->json([
                "status" => "failure",
                "status_code" => StatusCodes::BAD_REQUEST,
                "message" => 'Destination bank cannot be reached. Please try again Later',
            ],StatusCodes::BAD_REQUEST);
        }

        return response()->json([
            "status" => "failure",
            "status_code" => StatusCodes::BAD_REQUEST,
            "message" => $result['message'],
        ],StatusCodes::BAD_REQUEST);
    }

    public function confirmBankTransfer(Request $request)
    {

        $data = $request->TransactionRef;
        switch($request->TransferStatus){

            case 'success':
                Log::info("wallets-api-success". $data);
                $this->updateTransfer($data);
                return http_response_code(200);

            case 'failed':
                Log::info("wallets-api-reversal". $data);
                $this->doReversal($data);
                return http_response_code(200);

        }

    }

}

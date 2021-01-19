<?php

namespace App\Http\Controllers\Api;

use App\models\Card;
use Stripe\StripeClient;
use Illuminate\Http\Request;
use App\Collections\StatusCodes;
use App\Http\Controllers\Controller;
use App\Http\Requests\CardPaymentRequest;
use App\Http\Controllers\Api\CardTransactionController;
use App\Http\Controllers\Api\CommonFunctionsController;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
    public function cardPayment(CardPaymentRequest $request){
        $validatedData = $request->validated();
        $id = $validatedData['card_id'];
        $amount = $validatedData['amount']* 100;
        $description = $validatedData['description'];
        $card = Card::find($id);

        try{
            // \Stripe\Stripe::setApiKey('sk_test_51I8w9TFSTnpmya5shzFVTfBqBl5hefx32VScM5aZJfSOysNrnhMF9qtcKtnywOqbvHpRd5F6ZprE04wmYll0Cxyl00hzXI0HH5');
            // $stripe = new StripeClient("sk_test_51I8w9TFSTnpmya5shzFVTfBqBl5hefx32VScM5aZJfSOysNrnhMF9qtcKtnywOqbvHpRd5F6ZprE04wmYll0Cxyl00hzXI0HH5");
            $stripe = new \Stripe\StripeClient("sk_test_51I8w9TFSTnpmya5shzFVTfBqBl5hefx32VScM5aZJfSOysNrnhMF9qtcKtnywOqbvHpRd5F6ZprE04wmYll0Cxyl00hzXI0HH5");
            // $response = \Stripe\Balance::retrieve();
            // print_r($response);

            // die();
            try{

                $token = $stripe->tokens->create([
                    'card' => [
                      'number' => $card->cardNo,
                      'exp_month' => $card->cardExpiringMonth,
                      'exp_year' => $card->cardExpiringYear,
                      'cvc' => $card->cardCVV,
                    ],
                  ]);
            }catch(\Stripe\Exception\CardException $e){
                $commonFunction = new CommonFunctionsController;
                $array_json_return =$commonFunction->stripe_default_fail_response(__function__, $e);
                return response()->json($array_json_return, StatusCodes::BAD_REQUEST);
            }catch(\Stripe\Exception\ApiErrorException $e){
                $commonFunction = new CommonFunctionsController;
                $array_json_return =$commonFunction->stripe_default_fail_response(__function__, $e);
                return response()->json($array_json_return, StatusCodes::BAD_REQUEST);
            }


            try{
                $result =  $stripe->charges->create([
                    'amount' => $amount,
                    'currency' => 'usd',
                    'source' => $token,
                    'description' => $description,
                    'receipt_email' => Auth()->user()->email
                  ]);

                  $transaction = new CardTransactionController();
                  $response = $transaction->store([
                      'user_id' =>Auth()->user()->id,
                      'trans_id' =>$result->id,
                      'description' =>$result->description,
                      'amount' => $request->amount,
                      'receipt_url' => $result->receipt_url,
                      'receipt_no' => $result->receipt_number,
                      'card_details' => $result->payment_method_details->card->network .'-'. $result->payment_method_details->card->last4 

                  ]);
        
                return response()->json([
                    "status" => "success",
                    "message" => "Card charged successfully.",
                    'response'=> $response
                ]);
            }catch(\Stripe\Exception\CardException $e){
                $commonFunction = new CommonFunctionsController;
                $array_json_return =$commonFunction->stripe_default_fail_response(__function__, $e);
                return response()->json($array_json_return, StatusCodes::BAD_REQUEST);
            }

        }catch(\Exception $e){
            $commonFunction = new CommonFunctionsController;
            $array_json_return =$commonFunction->api_default_fail_response(__function__, $e);
            return response()->json($array_json_return, StatusCodes::BAD_REQUEST);
        }

    }
}

<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Collections\Constants;
use App\Collections\StatusCodes;
use App\Traits\CardPaymentTrait;
use App\Jobs\StripeFundWalletJob;
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
        $validatedData['trans_type'] = 1;
        $validatedData['user'] = Auth()->user()->id;
        $stripe = new \Stripe\StripeClient("sk_test_51I8w9TFSTnpmya5shzFVTfBqBl5hefx32VScM5aZJfSOysNrnhMF9qtcKtnywOqbvHpRd5F6ZprE04wmYll0Cxyl00hzXI0HH5");
        $response = $this->chargeCard($validatedData, $stripe);

        if ($response['code'] == 0){
            $commonFunction = new CommonFunctionsController;
            $array_json_return =$commonFunction->api_default_fail_response(__function__, $response['result']);
            return response()->json($array_json_return, StatusCodes::BAD_REQUEST);
        }
        // $this->creditWallet(Auth()->user()->id, $amount, $description);
        return response()->json([
            "status" => "success",
            "status_code" => StatusCodes::SUCCESS,
            "message" => "Wallet funded successfully.",
            'response'=> $response['result']
        ],StatusCodes::SUCCESS);

    }

    public function fundWalletPayStack(FundWalletRequest $request){
        // $validatedData = $request->validated();
        // $id = $validatedData['card_id'];
        // $amount = $validatedData['amount'];
        // $description = 'funded your wallet with '. $request->amount;
        // $validatedData['description'] = $description;
        // $stripe = new \Stripe\StripeClient("sk_test_51I8w9TFSTnpmya5shzFVTfBqBl5hefx32VScM5aZJfSOysNrnhMF9qtcKtnywOqbvHpRd5F6ZprE04wmYll0Cxyl00hzXI0HH5");
        // $response = $this->chargeCard($validatedData, $stripe);

        // if ($response['code'] == 0){
        //     $commonFunction = new CommonFunctionsController;
        //     $array_json_return =$commonFunction->api_default_fail_response(__function__, $response['result']);
        //     return response()->json($array_json_return, StatusCodes::BAD_REQUEST);
        // }
        // // $this->creditWallet(Auth()->user()->id, $amount, $description);
        // return response()->json([
        //     "status" => "success",
        //     "status_code" => StatusCodes::SUCCESS,
        //     "message" => "Wallet funded successfully.",
        //     'response'=> $response['result']
        // ],StatusCodes::SUCCESS);

    }

    public function valentine(){
    // Set your secret key. Remember to switch to your live secret key in production.
    // See your keys here: https://dashboard.stripe.com/account/apikeys
    \Stripe\Stripe::setApiKey(env('STRIPE_API_KEY'));
    
    // If you are testing your webhook locally with the Stripe CLI you
    // can find the endpoint's secret by running `stripe listen`
    // Otherwise, find your endpoint's secret in your webhook settings in the Developer Dashboard
    $endpoint_secret = env('STRIPE_WEBHOOK_CLIENT_SECRET');
    
    $payload = @file_get_contents('php://input');
    $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
    $event = null;
    
    try {
        $event = \Stripe\Webhook::constructEvent(
            $payload, $sig_header, $endpoint_secret
        );
    } catch(\UnexpectedValueException $e) {
        // Invalid payload
        http_response_code(400);
        exit();
    } catch(\Stripe\Exception\SignatureVerificationException $e) {
        // Invalid signature
        http_response_code(400);
        exit();
    }
    
    // Handle the event
    switch ($event->type) {
        case 'charge.succeeded':
            $paymentIntent = $event->data->object; // contains a \Stripe\PaymentIntent
            // echo "seen";
            dispatch(new StripeFundWalletJob($paymentIntent));
            // handlePaymentIntentSucceeded($paymentIntent);
            break;
        case 'payment_method.attached':
            $paymentMethod = $event->data->object; // contains a \Stripe\PaymentMethod
            // handlePaymentMethodAttached($paymentMethod);
     
    
           break;
        // ... handle other event types
        default:
            echo 'Received unknown event type ' . $event->type;
    }
    
    http_response_code(200);
    }
}

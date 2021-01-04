<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Collections\StatusCodes;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use App\Http\Requests\AccountNumberRequest;

class BankDetailsController extends Controller
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

    public function getCommercialBanks()
    {
        $response = Http::withoutVerifying([
            'Authorization'=> 'Bearer ' . env('PAYSTACK_SECRET_KEY'),
            'Content-Length'=> 100,
        ])->get(env('API_BASE_URL','https://api.paystack.co/').'bank',[
            "currency"=> "NGN",
            "country"=> "nigeria",
        ]);
        
        if ($response->status() == StatusCodes::SUCCESS){

            $result = $response->json();
            return response()->json([
                "status" =>"success",
                "status_code" =>StatusCodes::SUCCESS,
                "message" =>"banks retrieved",
                "data" =>$result['data']
            ],StatusCodes::SUCCESS);
        }
    }

    public function resolveAccountNumber(AccountNumberRequest $request)
    {

        
        $validatedData = $request->validated();

        $response = Http::withHeaders([
            'Authorization'=> 'Bearer ' . env('PAYSTACK_SECRET_KEY'),
            'Content-Length'=> 100,
        ])->get(env('API_BASE_URL','https://api.paystack.co/').'bank/resolve',[
            "account_number"=> $validatedData['account_number'],
            "bank_code"=> $validatedData['bank_code'],
        ]);

        if ($response->status() == StatusCodes::SUCCESS){

            $result = $response->json();
            return response()->json([
                "status" =>"success",
                "status_code" =>StatusCodes::SUCCESS,
                "message" =>"account retrieved",
                "data" =>$result['data']
            ],StatusCodes::SUCCESS);
        }else{
            $result = $response->json();
            return response()->json([
                "status" =>"failure",
                "status_code" =>StatusCodes::BAD_REQUEST,
                "message" =>$result['message']
            ],StatusCodes::BAD_REQUEST);
        }
    }

    public function resolveAccount(){
  $curl = curl_init();
  curl_setopt_array($curl, array(

    CURLOPT_URL => "https://api.paystack.co/bank/resolve?account_number=0689928729&bank_code=044",

    CURLOPT_RETURNTRANSFER => true,

    CURLOPT_ENCODING => "",

    CURLOPT_MAXREDIRS => 10,

    CURLOPT_TIMEOUT => 30,

    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,

    CURLOPT_CUSTOMREQUEST => "GET",

    CURLOPT_HTTPHEADER => array(

      "Authorization: Bearer sk_test_1b09bbe3bf87631c4513fdc1ed511501e7c5441b",

      "Cache-Control: no-cache",

    ),

  ));

  $response = curl_exec($curl);

  $err = curl_error($curl);

  

  curl_close($curl);

  

  if ($err) {

    echo "cURL Error #:" . $err;

  } else {
    echo ('ji');
    echo $response;

  }
    }
}

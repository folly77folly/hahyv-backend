<?php

namespace App\Http\Controllers\Api;

use GuzzleHttp\Client;
use App\Models\BankDetail;
use Illuminate\Http\Request;
use App\Collections\StatusCodes;
use App\Traits\BankDetailsTrait;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use App\Http\Requests\BankDetailsRequest;
use App\Http\Requests\AccountNumberRequest;
use App\Http\Controllers\Api\CommonFunctionsController;
use Exception;

class BankDetailsController extends Controller
{
    use BankDetailsTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        try{

            $id = Auth()->user()->id;
            $user_banks = BankDetail::where('user_id', $id)->with(['country', 'user'])->get();
            return response()->json([
                "status" =>"success",
                "status_code" =>StatusCodes::SUCCESS,
                "message" =>"banks retrieved",
                "data" =>$user_banks
            ],StatusCodes::SUCCESS);
        }catch(Exception $e){
            $commonFunction = new CommonFunctionsController;
            $array_json_return =$commonFunction->api_default_fail_response(__function__, $e);
            return response()->json($array_json_return, StatusCodes::BAD_REQUEST);
        }
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
    public function store(BankDetailsRequest $request)
    {
        //
        $id = Auth()->user()->id;
        $validatedData = $request->validated();
        try{
            $bvn = $validatedData['bvn'];
            $mobile = $validatedData['phone_no'];
            $bankCode = $validatedData['bank_id'];
            $accountNumber = $validatedData['account_no'];
            $firstName = $validatedData['first_name'];
            $lastName = $validatedData['last_name'];
            // $response  = $this->check($bvn, $mobile);
            $fields = [
                'bvn' => $bvn,
                'account_number' => $accountNumber,
                'bank_code' => $bankCode,
                'first_name' => $firstName,
                'last_name' => $lastName,

              ];
            $response  = $this->verifyMatch($fields, $accountNumber);
            if($response['status'] == false){
                return response()->json([
                    "status" =>"failure",
                    "status_code" =>StatusCodes::BAD_REQUEST,
                    "message" =>$response['message']
                ],StatusCodes::BAD_REQUEST);
            }
            $validatedData['user_id'] = $id;
            $validatedData['account_name'] = $validatedData['first_name'].' '.$validatedData['last_name'];
            $bank_details = BankDetail::updateOrCreate(['user_id' => $id],$validatedData);
            return response()->json([
                "status" =>"success",
                "status_code" =>StatusCodes::SUCCESS,
                "message" =>"bank information saved",
                "data" =>$bank_details->load(['country', 'user'])
            ],StatusCodes::SUCCESS);
        }catch(Exception $e){
            $commonFunction = new CommonFunctionsController;
            $array_json_return =$commonFunction->api_default_fail_response(__function__, $e);
            return response()->json($array_json_return, StatusCodes::BAD_REQUEST);
        }

    }

    public function saveBankDetails(BankDetailsRequest $request)
    {
        $id = Auth()->user()->id;
        $validatedData = $request->validated();
        try{
            $bvn = $validatedData['bvn'];
            $mobile = $validatedData['phone_no'];
            $bankCode = $validatedData['bank_id'];
            $accountNumber = $validatedData['account_no'];
            $firstName = $validatedData['first_name'];
            $lastName = $validatedData['last_name'];

            // $response  = $this->check($bvn, $mobile);
            $fields = [
                'bvn' => $bvn,
                'account_number' => $accountNumber,
                'bank_code' => $bankCode,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'phone_number'=> $mobile,

              ];
              //check bvn
            // $response  = $this->verifyMatch($fields);

            // if($response['status'] == false){
            //     return response()->json([
            //         "status" =>"failure",
            //         "status_code" =>StatusCodes::BAD_REQUEST,
            //         "message" =>$response['message']
            //     ],StatusCodes::BAD_REQUEST);
            // }

            $validatedData['user_id'] = $id;
            $validatedData['account_name'] = $validatedData['first_name'].' '.$validatedData['last_name'];
            $bank_details = BankDetail::updateOrCreate(['user_id' => $id],$validatedData);
            return response()->json([
                "status" =>"success",
                "status_code" =>StatusCodes::SUCCESS,
                "message" =>"bank information saved",
                "data" =>$bank_details->load(['country', 'user'])
            ],StatusCodes::SUCCESS);
        }catch(Exception $e){
            $commonFunction = new CommonFunctionsController;
            $array_json_return =$commonFunction->api_default_fail_response(__function__, $e);
            return response()->json($array_json_return, StatusCodes::BAD_REQUEST);
        }
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
        try{
            $user_bank = BankDetail::find($id)->load(['country', 'user']);
            return response()->json([
                "status" =>"success",
                "status_code" =>StatusCodes::SUCCESS,
                "message" =>"bank retrieved",
                "data" =>$user_bank
            ],StatusCodes::SUCCESS);
        }catch(Exception $e){
            $commonFunction = new CommonFunctionsController;
            $array_json_return =$commonFunction->api_default_fail_response(__function__, $e);
            return response()->json($array_json_return, StatusCodes::BAD_REQUEST);
        }

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
    public function update(BankDetailsRequest $request, $id)
    {
        //
        $user_id = Auth()->user()->id;
        $validatedData = $request->validated();
        try{
            $bank_details = BankDetail::where('id', $id)->first();
            $bank_details->update($validatedData);
            return response()->json([
                "status" =>"success",
                "status_code" =>StatusCodes::SUCCESS,
                "message" =>"bank information updated",
                "data" =>$bank_details->load(['country', 'user'])
            ],StatusCodes::SUCCESS);
        }catch(Exception $e){
            $commonFunction = new CommonFunctionsController;
            $array_json_return =$commonFunction->api_default_fail_response(__function__, $e);
            return response()->json($array_json_return, StatusCodes::BAD_REQUEST);
        }
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
        try{
            $bank_details = BankDetail::find($id);
            if($bank_details){
                $bank_details->destroy($id);
                return response()->json([
                    "status" =>"success",
                    "status_code" =>StatusCodes::SUCCESS,
                    "message" =>"bank deleted successfully",
                ],StatusCodes::SUCCESS);
            }
            return response()->json([
                "status" =>"failure",
                "status_code" =>StatusCodes::NOT_FOUND,
                "message" =>"bank record not found",
            ],StatusCodes::NOT_FOUND);
        }catch(Exception $e){
            $commonFunction = new CommonFunctionsController;
            $array_json_return =$commonFunction->api_default_fail_response(__function__, $e);
            return response()->json($array_json_return, StatusCodes::BAD_REQUEST);
        }


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

    public function getCommercialBanksWalletsApi(){
        $response = Http::withOptions([
            'verify'=>false,
        ])->withHeaders([
            'Authorization'=> 'Bearer ' . env('Public_Key', 'uvjqzm5xl6bw'),
        ])->post(env('API_BASE_URL','https://sandbox.wallets.africa/').'transfer/banks/all',[

        ]);
        
        return response()->json([
            "status" =>"success",
            "status_code" =>StatusCodes::SUCCESS,
            "message" =>"banks retrieved",
            "data" =>$response->json()
        ],StatusCodes::SUCCESS);

    }

    public function resolveAccountNumber(AccountNumberRequest $request)
    {

        $path = app_path().'\cacert.pem';
        // echo $path;
        $validatedData = $request->validated();
        $headers= [
            'Authorization' => 'Bearer ' . env('PAYSTACK_SECRET_KEY'),
            'Content-Length'=> 100,
            'Content-Type' => 'application/json'
        ];
        // Create a client with a base URI
        $client = new Client([
            'base_uri' => 'https://api.paystack.co/',
            'headers' => $headers,
            'verify' => $path,
            'VERIFY_PEER' => true,
            'http_errors' => false,
            'cert' => [$path,'changeit']
            ]);
        // Send a request to https://foo.com/api/test
        $response = $client->request('GET', 'bank/resolve?account_number=0689928729&bank_code=044');
        print_r(json_encode($response));
    }

    public function resolveAccount(AccountNumberRequest $request){
        $validatedData = $request->validated();

        $url = env('API_BASE_URL','https://api.paystack.co/').'bank/resolve?account_number='.$validatedData['account_number'].'&'.'bank_code='.$validatedData['bank_code'];
        // initialize curl session
        $curl = curl_init($url);


        //set curl options
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPGET, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer sk_test_1b09bbe3bf87631c4513fdc1ed511501e7c5441b",
            'Content-Type: application/json',
            "Accept: application/json",

        ]);

        $response = json_decode(curl_exec($curl));
        curl_close($curl);
        if(isset($response->status)){

            if ($response->status == 1){
        
                return response()->json([
                    'status' =>'success',
                    'status_code' => StatusCodes::SUCCESS,
                    'message' => 'account retrieved',
                    'data'=> $response->data
                ],StatusCodes::SUCCESS);
            }else{
                return response()->json([
                    'status' =>'failure',
                    'status_code' => StatusCodes::BAD_REQUEST,
                    'message' => $response->message,
                ],StatusCodes::BAD_REQUEST);
            }
        }else{
            return response()->json([
                'status' =>'failure',
                'status_code' => StatusCodes::BAD_REQUEST,
                'message' => "Error connecting to host bank. try again later",
            ],StatusCodes::BAD_REQUEST);
        }


    }    

    public function accountEnquiry(AccountNumberRequest $request)
    {
        $validatedData = $request->validated();

        $bankCode = $validatedData['bank_code'];
        $acctNo = $validatedData['account_number'];

        $response = Http::withOptions([
            'verify'=>false,
        ])->withHeaders([
            'Authorization'=> 'Bearer ' . env('Public_Key', 'uvjqzm5xl6bw'),
            'Content-type' => 'application/json',
            'Content-Length'=> 93,
        ])->post(env('API_BASE_URL','https://sandbox.wallets.africa/').'transfer/bank/account/enquire',[
            "BankCode"=> $bankCode,
            "AccountNumber"=> $acctNo,
            'SecretKey'=>env('Secret_Key', 'hfucj5jatq8h'),
        ]);

        $arrayResponse = $response->json();
        if ($response->status() == StatusCodes::SUCCESS){
            //structure response
            $result = [
                'account_number'=>$arrayResponse['AccountNumber'],
                'account_name'=>$arrayResponse['AccountName'],
                'bank_id'=>$arrayResponse['BankCode'],
            ];

            //Show Response
            return response()->json([
                'status' =>'success',
                'status_code' => StatusCodes::SUCCESS,
                'message' => 'account retrieved',
                'data'=> $result
            ],StatusCodes::SUCCESS);
            
        }
        return response()->json([
            'status' =>'failure',
            'status_code' => StatusCodes::BAD_REQUEST,
            'message' => $arrayResponse['Message'],
        ],StatusCodes::BAD_REQUEST);


    }

    public function verifyBvnMatch($fields)
    {
        // dd($fields);
        $response = Http::withOptions([
            'verify'=>false
        ])->withHeaders([
            'Authorization'=> 'Bearer ' . env('Public_Key', 'uvjqzm5xl6bw'),
            'Content-Length'=> 100,
        ])->post(env('API_BASE_URL','https://sandbox.wallets.africa/').'account/resolvebvn',[
            "bvn"=> $fields['bvn'],
            'SecretKey'=>env('Secret_Key', 'hfucj5jatq8h'),
        ]);
        $data = $response->json();
        dd($data);
        if ($response->status() == StatusCodes::SUCCESS){
             if($data['phoneNumber'] == $fields['phone_number'])
             {
                 return [
                     'status'=>true
                 ];
             }
             return [
                'status'=>false,
                'message' => 'Your phone number does not match your BVN records'
            ];
        }
        return [
            'status'=>false,
            'message' => $data['Message']
        ];

    }


    // public function check($bvn, $mobile)
    // {
    //     $response = $this->resolveBvn3($bvn);

    //     if(isset($response->status)){
    //         if($response->status == true){
    //             if($response->data->mobile == $mobile){

    //                 $result = [
    //                     'status' => true,
    //                 ];
    //             }else{
    //                 $result = [
    //                     'status' => false,
    //                     'message' => "Your bvn registered phone number doesn't match"
    //                 ];
    //             }
    //             return($result);
    //         }else{
    //             if($response->message == "Free limit reached. You must be in live mode to make this request" ){
    //                 $result = [
    //                     'status' => false,
    //                     'message' => "Can't verify your bvn at the moment try again Later"
    //                 ];
    //                 return($result);
    //             }
    //             $result = [
    //                 'status' => false,
    //                 'message' => $response->message
    //             ];
    //             return($result);
    //         }
    //     }else{

    //         $result = [
    //             'status' => false,
    //             'message' => "Can't Verify your bvn now try again Later"
    //         ];
    //         return($result);
    //     }
    // }

    public function verifyMatch($fields, $accountNumber){
        $fieldQuery = http_build_query($fields);
        $response = $this->matchBvn($fieldQuery);
        // return $response;
        if(isset($response->status)){
            if($response->status == true){
                if($response->data->account_number == true and $response->data->is_blacklisted == false){

                    $result = [
                        'status' => true,
                    ];
                }else{
                    $result = [
                        'status' => false,
                        'message' => "Your BVN information does not match your bank account information"
                    ];
                }
                return($result);
            }else{
                if($response->message == "BVN match requests exhausted for the month" ){
                    $result = [
                        'status' => false,
                        'message' => "Can't verify your BVN at the moment limit try again Later"
                    ];
                    return($result);
                }
                $result = [
                    'status' => false,
                    'message' => $response->message
                ];
                return($result);
            }
        }else{

            $result = [
                'status' => false,
                'message' => "Can't Verify your bvn now try again Later"
            ];
            return($result);
        }
    }


}

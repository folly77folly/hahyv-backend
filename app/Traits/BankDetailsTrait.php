<?php
namespace App\Traits;
use GuzzleHttp\Client;
use App\Collections\Constants;
use Illuminate\Support\Facades\Http;

Trait BankDetailsTrait{

    public function resolveBvn($bvn){

        // $response = Http::get('https://api.paystack.co/bank/resolve_bvn/:12345678909');
        $response = Http::withHeaders([
            'Authorization'=> 'Bearer ' . env('PAYSTACK_SECRET_KEY'),
            'Content-Length'=> 100,
        ])->get(env('API_BASE_URL','https://api.paystack.co/').'bank/resolve_bvn',[
            "BVN" => "1234567890",
        ]);
        
        if ($response->status() == StatusCodes::SUCCESS){

            $result = $response->json();
            return $result['data'];
        }
    }

    public function resolveBvn2($bvn){
        $path = app_path().'\cacert.pem';
        $path0 = app_path().'\cert.pem';
        $path1 = app_path().'\locohozt.pem';
        $path2 = app_path().'\locohozt.key';
        $path3 = app_path().'\key.pem';
        echo $path0;
        // $validatedData = $request->validated();
        $headers= [
            'Authorization' => 'Bearer ' . env('PAYSTACK_SECRET_KEY'),
            'Content-Length'=> 100,
            'Content-Type' => 'application/json'
        ];
        // Create a client with a base URI
        $client = new Client([
            'base_uri' => 'https://api.paystack.co/',
            'headers' => $headers,
            'verify' => $path0,
            'VERIFY_PEER' => true,
            'http_errors' => false,
            'cert' => $path,
            'ssl_key' => $path
            ]);
        // Send a request to https://foo.com/api/test
        $response = $client->request('GET', 'bank/resolve?account_number=0689928729&bank_code=044');
        $response2 = json_encode($response);
        return $response2;

    }

    public function resolveBvn3($bvn){

        $curl = curl_init();
        // echo env('PAYSTACK_BASE_URL')."bank/resolve_bvn/$bvn";
        curl_setopt_array($curl, array(
          CURLOPT_URL => env('PAYSTACK_BASE_URL')."bank/resolve_bvn/$bvn",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "GET",
          CURLOPT_HTTPHEADER => array(
            "Authorization: Bearer ". env('PAYSTACK_SECRET_KEY'),
            "Cache-Control: no-cache",
          ),
        ));
        
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        
        if ($err) {
            return $err;
        //   echo "cURL Error #:" . $err;
        } else {
          return json_decode($response);
        }
    }

    
}
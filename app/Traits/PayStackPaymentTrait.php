<?php
namespace App\Traits;

use App\Models\Card;
use App\Collections\Constants;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Api\CardTransactionController;

Trait PayStackPaymentTrait{


    
    public function transferRecipient(array $fields ){
        $url = env('PAYSTACK_BASE_URL')."transferrecipient";
        $key = env('PAYSTACK_SECRET_KEY_TEST');
        $fields_string = http_build_query($fields);

        //open connection
      
        $ch = curl_init();
      
        
      
        //set the url, number of POST vars, POST data
      
        curl_setopt($ch,CURLOPT_URL, $url);
      
        curl_setopt($ch,CURLOPT_POST, true);
      
        curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
      
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
      
          "Authorization: Bearer $key",
      
          "Cache-Control: no-cache",
      
        ));
      
        
      
        //So that curl_exec returns the contents of the cURL; rather than echoing it
      
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 
      
        
      
        //execute post
      
        $result = curl_exec($ch);
      
        return json_decode($result);
    }

    public function transfer(array $fields){
        $url = env('PAYSTACK_BASE_URL')."transfer";
        $fields_string = http_build_query($fields);
        $key = env('PAYSTACK_SECRET_KEY_TEST');
        //open connection
      
        $ch = curl_init();
      
        
      
        //set the url, number of POST vars, POST data
      
        curl_setopt($ch,CURLOPT_URL, $url);
      
        curl_setopt($ch,CURLOPT_POST, true);
      
        curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
      
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
      
          "Authorization: Bearer $key",
      
          "Cache-Control: no-cache",
      
        ));
      
        
      
        //So that curl_exec returns the contents of the cURL; rather than echoing it
      
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 
      
        
      
        //execute post
      
        $result = curl_exec($ch);
      
        return json_decode($result);
    }
}
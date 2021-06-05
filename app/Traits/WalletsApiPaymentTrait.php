<?php
namespace App\Traits;

use App\Models\Card;
use App\Collections\Constants;
use App\Collections\StatusCodes;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Api\CardTransactionController;

Trait WalletsApiPaymentTrait{


    public function doTransfer(array $fields)
    {
      
      $response = Http::withOptions([
        'verify'=>false,
      ])->withHeaders([
          'Authorization'=> 'Bearer ' .env('Public_Key', 'uvjqzm5xl6bw'),
          'Content-Length'=> 100,
      ])->post(env('API_BASE_URL','https://sandbox.wallets.africa/').'transfer/bank/account', $fields);

      // $responses = $this->checkBankTransferDetails($fields['TransactionReference']);

      $result = $response->json();
      if ($response->status() == StatusCodes::SUCCESS and $result['ResponseCode'] == StatusCodes::SUCCESS ){
        return [
          'status'=>true,
        ];
      }
      return [
        'status'=>false,
        'message'=> $result['Message']
      ];

    }

    public function checkBankTransferDetails($refNo)
    {
      $fields=[
        'TransactionReference' => $refNo,
        'SecretKey'=>env('Secret_Key', 'hfucj5jatq8h'),
      ];
      // return $fields;
      $response = Http::withOptions([
        'verify'=>false,
      ])->withHeaders([
          'Authorization'=> 'Bearer ' .env('Public_Key', 'uvjqzm5xl6bw'),
          'Content-Length'=> 100,
      ])->post(env('API_BASE_URL','https://sandbox.wallets.africa/').'transfer/bank/details', $fields);
      return $response->json();

      // $result = $response->json();

      // if ($response->status() == StatusCodes::SUCCESS and $result['ResponseCode'] == StatusCodes::SUCCESS ){
      //   return [
      //     'status'=>true,
      //   ];
      // }
      // return [
      //   'status'=>false,
      //   'message'=> $result['Message']
      // ];

    }
}
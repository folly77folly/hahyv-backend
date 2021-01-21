<?php
namespace App\Traits;

use App\models\Card;
use App\Collections\Constants;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Api\CardTransactionController;
Trait CardPaymentTrait{


    
    public function getCardToken(int $id, $stripe ){
            $card = Card::find($id);
            try{

                $token = $stripe->tokens->create([
                    'card' => [
                      'number' => $card->cardNo,
                      'exp_month' => $card->cardExpiringMonth,
                      'exp_year' => $card->cardExpiringYear,
                      'cvc' => $card->cardCVV,
                    ],
                  ]);

                  return [
                    'code' => 1,
                    'result' => $token->id
                ];
            }catch(\Stripe\Exception\CardException $e){
                return [
                    'code' => 0,
                    'result' => $e
                ];
            }catch(\Stripe\Exception\ApiErrorException $e){
                return [
                    'code' => 0,
                    'result' => $e
                ];return $e;
            }
    }

    public function chargeCard(array $validatedData, $stripe){
        $id = $validatedData['card_id'];
        $amount = $validatedData['amount']* Constants::STRIPE_VALUE;
        $description = $validatedData['description'];
        $token = $this->getCardToken($id, $stripe);
        if ($token['code'] == 0){
            return $token['result'];
        }
        try{
            $result =  $stripe->charges->create([
                'amount' => $amount,
                'currency' => Constants::CURRENCY,
                'source' => $token['result'],
                'description' => $description,
                'receipt_email' => Auth()->user()->email
              ]);

              $transaction = new CardTransactionController();
              $response = $transaction->store([
                  'user_id' =>Auth()->user()->id,
                  'trans_id' =>$result->id,
                  'description' =>$result->description,
                  'amount' => $validatedData['amount'],
                  'receipt_url' => $result->receipt_url,
                  'receipt_no' => $result->receipt_number,
                  'card_details' => $result->payment_method_details->card->network .'-'. $result->payment_method_details->card->last4 

              ]);
                return [
                    'code' => 1,
                    'result' => $response
                ];

        }catch(\Stripe\Exception\CardException $e){
            return [
                'code' => 0,
                'result' => $e
            ];
        }
    }
}
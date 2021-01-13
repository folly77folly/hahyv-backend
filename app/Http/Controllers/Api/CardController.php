<?php

namespace App\Http\Controllers\Api;

use App\Collections\StatusCodes;
use App\Http\Controllers\Controller;
use App\Models\Card;
use Illuminate\Http\Request; 
use App\Http\Requests\CardVerificationRequest;
use App\Http\Requests\CardVerifyRequest;
use App\User;


class CardController extends Controller
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
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\CardVerificationRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CardVerificationRequest $request)
    {

        $validatedData = $request->validated();

        $CardNumber = $validatedData["card_number"];
        

        $cardType = $this->getCardBrand($CardNumber);

        $user_id = Auth()->user()->id;

        $newCard = new Card;
        
        $newCard->cardName = $cardType;
        $newCard->cardNo = $validatedData["card_number"];
        $newCard->cardExpiringMonth = $validatedData["expiration_month"];
        $newCard->cardExpiringYear = $validatedData["expiration_year"];
        $newCard->cardCVV = $validatedData["cvc"];
        $newCard->user_id = $user_id;

        $newCard->save();

        return response()->json([
            "status" => "success",
            "message" => "Card saved successfully.",
            "data" => $newCard->load('user')
        ], StatusCodes::SUCCESS);
    }

    public function userCards()
    {
        $user_id = Auth()->user()->id;

        $cards = Card::where('user_id', $user_id)->get();
        $noOfCards = Card::where('user_id', $user_id)->count();

        if($noOfCards == 0) {
            return response()->json([
                "status" => "failue",
                "message" => "You have not added a card yet."
            ], StatusCodes::NOT_FOUND);
        }

        return response()->json([
            "status" => "success",
            "message" => "Cards retrieved successfully.",
            "cards" => $cards->load('user')
        ], StatusCodes::SUCCESS);
        
    }

    public function editCard(CardVerifyRequest $request, $id)
    {
        $validatedData = $request->validated();

        $CardNumber = $validatedData["card_number"];

        $card = Card::where('id', $id)->first();

        if(!$card) {
            return response()->json([
                "status" => "failure",
                "message" => "Card not found."
            ], StatusCodes::NOT_FOUND);

        }

        $cardType = $this->getCardBrand($CardNumber);

        $user_id = Auth()->user()->id;
                
        $card->cardName = $cardType;
        $card->cardNo = $validatedData["card_number"];
        $card->cardExpiringMonth = $validatedData["expiration_month"];
        $card->cardExpiringYear = $validatedData["expiration_year"];
        $card->cardCVV = $validatedData["cvc"];
        $card->user_id = $user_id;

        $card->save();

        return response()->json([
            "status" => "success",
            "message" => "Cards updated successfully.",
            "cards" => $card->load('user')
        ], StatusCodes::SUCCESS);
    }

    public function delete($id) 
    {
        $card = Card::where('id', $id)->first();

        if(!$card) {
            return response()->json([
                "status" => "failure",
                "message" => "Card not found."
            ], StatusCodes::NOT_FOUND);
        }

        $card->delete();

        return response()->json([
            "status" => "success",
            "message" => "Card deleted successfully." 
        ], StatusCodes::SUCCESS);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Card  $card
     * @return \Illuminate\Http\Response
     */
    public function show(Card $card)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Card  $card
     * @return \Illuminate\Http\Response
     */
    public function update(CardVerificationRequest $request, $card)
    {

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Card  $card
     * @return \Illuminate\Http\Response
     */
    public function destroy(Card $card)
    {
        //
    }

    /**
     * Obtain a brand constant from a PAN
     *
     * @param string $pan               Credit card number
     * @param bool   $include_sub_types Include detection of sub visa brands
     * @return string
     */
    public static function getCardBrand($pan, $include_sub_types = false)
    {
        //maximum length is not fixed now, there are growing number of CCs has more numbers in length, limiting can give false negatives atm

        //these regexps accept not whole cc numbers too
        //visa
        $visa_regex = "/^4[0-9]{0,}$/";
        $vpreca_regex = "/^428485[0-9]{0,}$/";
        $postepay_regex = "/^(402360|402361|403035|417631|529948){0,}$/";
        $cartasi_regex = "/^(432917|432930|453998)[0-9]{0,}$/";
        $entropay_regex = "/^(406742|410162|431380|459061|533844|522093)[0-9]{0,}$/";
        $o2money_regex = "/^(422793|475743)[0-9]{0,}$/";

        // MasterCard
        $mastercard_regex = "/^(5[1-5]|222[1-9]|22[3-9]|2[3-6]|27[01]|2720)[0-9]{0,}$/";
        $maestro_regex = "/^(5[06789]|6)[0-9]{0,}$/";
        $kukuruza_regex = "/^525477[0-9]{0,}$/";
        $yunacard_regex = "/^541275[0-9]{0,}$/";

        // American Express
        $amex_regex = "/^3[47][0-9]{0,}$/";

        // Diners Club
        $diners_regex = "/^3(?:0[0-59]{1}|[689])[0-9]{0,}$/";

        //Discover
        $discover_regex = "/^(6011|65|64[4-9]|62212[6-9]|6221[3-9]|622[2-8]|6229[01]|62292[0-5])[0-9]{0,}$/";

        //JCB
        $jcb_regex = "/^(?:2131|1800|35)[0-9]{0,}$/";

        //ordering matter in detection, otherwise can give false results in rare cases
        if (preg_match($jcb_regex, $pan)) {
            return "jcb";
        }

        if (preg_match($amex_regex, $pan)) {
            return "amex";
        }

        if (preg_match($diners_regex, $pan)) {
            return "diners_club";
        }

        //sub visa/mastercard cards
        if ($include_sub_types) {
            if (preg_match($vpreca_regex, $pan)) {
                return "v-preca";
            }
            if (preg_match($postepay_regex, $pan)) {
                return "postepay";
            }
            if (preg_match($cartasi_regex, $pan)) {
                return "cartasi";
            }
            if (preg_match($entropay_regex, $pan)) {
                return "entropay";
            }
            if (preg_match($o2money_regex, $pan)) {
                return "o2money";
            }
            if (preg_match($kukuruza_regex, $pan)) {
                return "kukuruza";
            }
            if (preg_match($yunacard_regex, $pan)) {
                return "yunacard";
            }
        }

        if (preg_match($visa_regex, $pan)) {
            return "visa";
        }

        if (preg_match($mastercard_regex, $pan)) {
            return "mastercard";
        }

        if (preg_match($discover_regex, $pan)) {
            return "discover";
        }

        if (preg_match($maestro_regex, $pan)) {
            if ($pan[0] == '5') { //started 5 must be mastercard
                return "mastercard";
            }
            return "maestro"; //maestro is all 60-69 which is not something else, thats why this condition in the end

        }

        return "unknown"; //unknown for this system
    }
}

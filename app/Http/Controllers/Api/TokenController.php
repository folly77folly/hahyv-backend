<?php

namespace App\Http\Controllers\Api;

use App\User;
use App\Models\TokenRate;
use Illuminate\Http\Request;
use App\Collections\StatusCodes;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Traits\TokenTransactionsTrait;
use App\Http\Requests\TokenRateRequest;
use App\Traits\WalletTransactionsTrait;

class TokenController extends Controller
{
    use WalletTransactionsTrait, TokenTransactionsTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $tokenRates = TokenRate::all();
        return response()->json([
            "status" => "success",
            "status_code" => StatusCodes::SUCCESS,
            "message" => "Token rate retrieved.",
            "data" => $tokenRates
        ], StatusCodes::SUCCESS);
    }

    public function buyTokenAmount(Request $request)
    {

        $request->validate([
            'amount' => 'required|integer',
        ]);

        $user = Auth()->user();

        $walletBalance = $user->walletBalance;
        
        $amountToPurchase = $request->amount;

        if ($amountToPurchase > $walletBalance) {
            return response()->json([
                "status" => "failue",
                "message" => "You have insufficient Wallet Balance to make the purchase.",
                "Wallet Balance" => $user->walletBalance,
                "Amount to Purchase" => (int)$amountToPurchase
            ], StatusCodes::UNPROCESSABLE);
        }


        $tokenRate = DB::table('token_rates')->latest()->first();

        $presentTokenRate = $tokenRate->dollarTokenRate;

        $tokenBalance = $user->tokenBalance;

        $tokenToPurchase = number_format((float)($amountToPurchase / $presentTokenRate), 2, '.', '');

        DB::transaction(function ()  use ($walletBalance, $user, $tokenBalance, $amountToPurchase, $tokenToPurchase) {
            $walletReduction  = $walletBalance - $amountToPurchase;

            DB::table('users')->where('id', $user->id)->update([
                'walletBalance' => $walletReduction,
                'tokenBalance' => $tokenBalance + $tokenToPurchase,
                'updated_at' => now()
            ]);

            DB::table('wallet_transactions')->insert([
                'user_id' => $user->id,
                'previousWalletBalance' => $walletBalance,
                'presentWalletBalance' => $walletReduction,
                'amountCredited' => 0,
                'amountDebited' => $amountToPurchase,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            DB::table('token_transactions')->insert([
                'user_id' => $user->id,
                'previousTokenBalance' => $tokenBalance,
                'presentTokenBalance' => $tokenBalance + $tokenToPurchase,
                'tokenCredited' => $tokenToPurchase,
                'tokenDebited' => 0,
                'created_at' => now(),
                'updated_at' => now()
            ]);

        });

        $user = User::find($user->id);

        return response()->json([
            "status" => "success",
            "message" => "Cards retrieved successfully.",
            "token Purchase" => $tokenToPurchase,
            'user' => $user
        ], StatusCodes::SUCCESS);
    }
    public function buyToken(Request $request)
    {

        $request->validate([
            'token' => 'required|integer',
        ]);
        $tokenRate = TokenRate::first();
        $unit = $tokenRate->unit;
        $modulo = fmod($request->token, $unit);

        if($modulo != 0){
            return response()->json([
                "status" => "failure",
                "status_code" => StatusCodes::UNPROCESSABLE,
                "message" => "You can only purchase in multiples of $unit.",
            ], StatusCodes::UNPROCESSABLE);
        }

        $user = Auth()->user();

        $tokenBalance = $user->tokenBalance;
        $walletBalance = $user->walletBalance;

        $tokenRate = DB::table('token_rates')->latest()->first();

        $presentTokenRate = $tokenRate->rate;

        $noOfTokenRequested = $request->token;  // $unit;
        $reference = "wa_tk".time();
        $description = "purchase of $noOfTokenRequested unit(s) of token";
        $calculateToken = $noOfTokenRequested * $presentTokenRate;
        $formattedAmount = number_format($calculateToken,2,".",",");


        if ($calculateToken > $walletBalance) {
            return response()->json([
                "status" => "failure",
                "status_code" => StatusCodes::UNPROCESSABLE,
                "message" => "You have insufficient Wallet Balance to make the purchase you will need $formattedAmount naira.",
            ], StatusCodes::UNPROCESSABLE);
        }

        $this->debitWallet($user->id, $calculateToken, $description, $reference);
        $this->creditToken($user->id, $noOfTokenRequested, $description, $reference);

        return response()->json([
            "status" => "success",
            "status_code" => StatusCodes::SUCCESS,
            "message" => "token purchased successfully.",
        ], StatusCodes::SUCCESS);
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
}

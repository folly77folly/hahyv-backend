<?php

namespace App\Http\Controllers\Api;

use App\Collections\StatusCodes;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\TokenRateRequest;
use App\User;

class TokenController extends Controller
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

    public function buyToken(Request $request)
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


        $tokenRate = DB::table('tokenrates')->latest()->first();

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
    public function buyTokenToken(Request $request)
    {

        $request->validate([
            'token' => 'required|integer',
        ]);

        $user = Auth()->user();

        $tokenBalance = $user->tokenBalance;
        $walletBalance = $user->walletBalance;

        $tokenRate = DB::table('tokenrates')->latest()->first();

        $presentTokenRate = $tokenRate->dollarTokenRate;

        $noOfTokenRequested = $request->token;


        $calculateToken = $noOfTokenRequested * $presentTokenRate;

        if ($calculateToken > $walletBalance) {
            return response()->json([
                "status" => "failue",
                "message" => "You have insufficient Wallet Balance to make the purchase.",
                "Wallet Balance" => $user->walletBalance,
                "Cost of Token" => $calculateToken
            ], StatusCodes::UNPROCESSABLE);
        }


        DB::transaction(function ()  use ($walletBalance, $user, $calculateToken, $tokenBalance, $noOfTokenRequested) {
            $walletReduction  = $walletBalance - $calculateToken;

            DB::table('users')->where('id', $user->id)->update([
                'walletBalance' => $walletReduction,
                'tokenBalance' => $tokenBalance + $noOfTokenRequested,
                'updated_at' => now()
            ]);

            DB::table('wallet_transactions')->insert([
                'user_id' => $user->id,
                'previousWalletBalance' => $walletBalance,
                'presentWalletBalance' => $walletReduction,
                'amountCredited' => 0,
                'amountDebited' => $calculateToken,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            DB::table('token_transactions')->insert([
                'user_id' => $user->id,
                'previousTokenBalance' => $tokenBalance,
                'presentTokenBalance' => $tokenBalance + $noOfTokenRequested,
                'tokenCredited' => $noOfTokenRequested,
                'tokenDebited' => 0,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        });


        return response()->json([
            "status" => "success",
            "message" => "Cards retrieved successfully.",
            "token Purchase" => $noOfTokenRequested,
            'user' => Auth()->user()
        ], StatusCodes::SUCCESS);
    }

    public function tokenRate(TokenRateRequest $request)
    {
        $rate = $request->rate;

        $tokenRate = DB::table('tokenrates')->first();

        if (!$tokenRate) {
            DB::table('tokenrates')->insert([
                'dollarTokenRate' => $rate,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            $newtokenRate = DB::table('tokenrates')->latest()->first();

            return response()->json([
                "status" => "success",
                "message" => "Token Rate added successfully.",
                "token" => $newtokenRate
            ], StatusCodes::SUCCESS);
        }

        DB::table('tokenrates')->update([
            'dollarTokenRate' => $rate,
            'updated_at' => now()
        ]);

        $updateRate = DB::table('tokenrates')->orderByDesc('tokenrates.updated_at')->first();

        return response()->json([
            "status" => "success",
            "message" => "Token Rate updated successfully.",
            "token" => $updateRate
        ], StatusCodes::SUCCESS);
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

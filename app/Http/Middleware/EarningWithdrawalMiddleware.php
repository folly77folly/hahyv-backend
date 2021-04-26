<?php

namespace App\Http\Middleware;

use Closure;
use App\Collections\StatusCodes;
use App\Http\Requests\WithRequest;
use Validator;
class EarningWithdrawalMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $validatedData = Validator::make($request->all(),[
           'amount' => ['required', 'numeric']
        ]);

        if ($validatedData->fails()){
            return response()->json([
                'status'=> 'failure',
                'status_code'=> StatusCodes::BAD_REQUEST,
                'message'=>'Invalid data',
                'errors'=> $validatedData->errors()
            ],StatusCodes::BAD_REQUEST);
        }
        $amount = $request->amount;
        

        if(Auth()->user()->availableEarning == 0){
            return response()->json([
                'status'=> 'failure',
                'status_code'=> StatusCodes::BAD_REQUEST,
                'message'=>'Your earning balance is zero'
            ],StatusCodes::BAD_REQUEST);
        }

        if(Auth()->user()->availableEarning < $amount){
            return response()->json([
                'status'=> 'failure',
                'status_code'=> StatusCodes::BAD_REQUEST,
                'message'=>'You cannot request more than available amount'
            ],StatusCodes::BAD_REQUEST);
        }

        return $next($request);
    }
}

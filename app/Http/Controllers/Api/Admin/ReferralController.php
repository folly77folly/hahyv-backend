<?php

namespace App\Http\Controllers\Api\Admin;

use App\User;
use App\Models\Referral;
use Illuminate\Http\Request;
use App\Collections\Constants;
use App\Models\ReferEarnSetup;
use App\Collections\StatusCodes;
use App\Http\Controllers\Controller;
use App\Http\Requests\ReferralRequest;
use App\Http\Requests\PayReferralRequest;

class ReferralController extends Controller
{
    //
    public function index()
    {
        $referrals = Referral::select('user_id')->distinct('user_id')->with(['user' => function($query){
            $query->with('bankDetail');
        }])->paginate(Constants::PAGE_LIMIT);
        
        return response()->json([
            "status"=> "success",
            "message"=>"Referral retrieved successfully",
            "data"=> $referrals
            ],StatusCodes::SUCCESS);
    }

    public function getSetup(){

        return response()->json([
        "status"=> "success",
            "message"=>"Referral retrieved successfully",
            "data"=> ReferEarnSetup::first()
            ],StatusCodes::SUCCESS);
    }

    public function setup(ReferralRequest $request)
    {
        try {
            //code...
            $validatedData = $request->validated();
            $setup  = ReferEarnSetup::updateOrCreate(['id'=> 1],$validatedData);
            return response()->json([
                "status"=> "success",
                "message"=>"Referral setup updated successfully",
                "data" => $setup
                ],StatusCodes::SUCCESS);

        } catch (Exception $e) {
            
            return response()->json([
                "status"=> "failure",
                "message"=>"Update not successful",
                ],StatusCodes::BAD_REQUEST);
        }
    }

    public function payReferral(PayReferralRequest $request)
    {
        $validatedData = $request->validated();
        $user = User::where('id', $request->user_id)->update(['referral_is_paid' => 1]);
        return response()->json([
            "status"=> "success",
            "message"=>"Referral paid successfully",
            "data" => User::find($request->user_id)
            ],StatusCodes::SUCCESS);
    }
}

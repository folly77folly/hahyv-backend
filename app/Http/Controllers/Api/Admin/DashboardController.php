<?php

namespace App\Http\Controllers\Api\Admin;

use App\User;
use App\Models\HahyvEarning;
use Illuminate\Http\Request;
use App\Collections\Constants;
use App\Jobs\SendAnnouncement;
use App\Mail\AnnouncementMail;
use App\Models\SubscribersList;
use App\Collections\StatusCodes;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\AnnouncementRequest;
use App\Http\Requests\DeactivateUserRequest;

class DashboardController extends Controller
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
    public function allUsers(){
        $users  = User::where('role_id', '!=', 1)->latest()->get();
        return response()->json([
            "status" => "success",
            "status_code" => StatusCodes::SUCCESS,
            "message" => "all users retrieved",
            "data" => $users
        ],StatusCodes::SUCCESS,);
    }

    public function allUsersP(){
        $users  = User::where('role_id', '!=', 1)->latest()->paginate(Constants::PAGE_LIMIT);
        return response()->json([
            "status" => "success",
            "status_code" => StatusCodes::SUCCESS,
            "message" => "all users retrieved",
            "data" => $users
        ],StatusCodes::SUCCESS,);
    }

    public function deactivateUser(DeactivateUserRequest $request){
        $validatedData = $request->validated();
        $user  = User::find($validatedData['user_id']);
        if($user->is_active == 1){
            $user->is_active = 0;
            DB::table('oauth_access_tokens')->where('user_id',$user->id)->update(['revoked'=> 1]);
        }else{
            $user->is_active = 1;
            DB::table('oauth_access_tokens')->where('user_id',$user->id)->update(['revoked'=> 0]);
        }
        $user->save();
        return response()->json([
            "status" => "success",
            "status_code" => StatusCodes::SUCCESS,
            "message" => "update successfully",
            "data" => $user
        ],StatusCodes::SUCCESS,);
    }

    public function sendMail(AnnouncementRequest $request){
        $mailDetails = $request->validated();
        $user  = User::find($mailDetails['user_id']);
        // $users  = User::where('role_id', '!=', 1)->pluck('email');
        // $users  = User::where('username', '!=', null)->pluck('email','username');
        $users  = User::where('role_id', '!=', 1)->pluck('email','username');

        \dispatch(new SendAnnouncement($mailDetails, $users));

        return response()->json([
            "status" => "success",
            "status_code" => StatusCodes::SUCCESS,
            "message" => "mail sent successfully",
        ],StatusCodes::SUCCESS,);
    }

    public function dashboard(){

        $users = User::where('role_id', 2)->get();
        $noOfUsers = $users->count();
        $noOfNonCreators = $users->where('is_monetize', 0)->count();
        $noOfCreators = $users->where('is_monetize', 1)->count();

        // no of subscribers
        $subscribers = SubscribersList::all();
        $allSubscribers = $subscribers->count();
        $activeSubscribers = $subscribers->where('is_active', '==', 1 )->count();
        $inActiveSubscribers = $subscribers->where('is_active', '==', 0)->count();

        //hahyv wallet
        $hahyvWallet = HahyvEarning::all()->sum('amount');

        //payout value
        // $payoutOuters = $users->where('availableEarning', '>', 0);
        $payout = $users->where('availableEarning', '>', 0)->sum('availableEarning');
        
        $data =  [
            "noOfUsers" => $noOfUsers,
            "noOfNonCreators" => $noOfNonCreators,
            "noOfCreators" => $noOfCreators,
            "noOfSubscribers" => $allSubscribers,
            "noOfActiveSubscribers" => $activeSubscribers,
            "noOfInActiveSubscribers" => $inActiveSubscribers,
            "hahyvWallet" => $hahyvWallet,
            "totalExpectedPayout" => $payout,
            "totalExpectedIncome" => $hahyvWallet- $payout,
        ];

        return response()->json([
            "status" => "success",
            "status_code" => StatusCodes::SUCCESS,
            "message" => "successfully",
            "data" => $data,
        ],StatusCodes::SUCCESS,);        

    }


    public function profileUsername(string $str){
        $post = User::where('username' ,$str)
        ->with(['post' => function($query){
                $query->with('Comment')->with(['user' => function($query){
                    $query->with([
                        'monetizeBenefits:user_id,benefits',
                        'subscriptionBenefits:user_id,benefits',
                        ])->with([
                            'subscriptionRates' => function($query){
                                $query->with('subscription:id,name,period');
                            }]);
                }])
                ->with(['polls'=>function($query){
                    return $query->with('votes');
                }])
                ->with(['likes' => function($query){
                    return $query->where('liked', 1)->with('user');
                }])->latest()->paginate(Constants::PAGE_LIMIT);

        }])->first();


        return response()->json([
            "status" => "success",
            "message" => "User profile fetched successfully.",
            "data" => $post
        ], StatusCodes::SUCCESS);
    }
}

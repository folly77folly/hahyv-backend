<?php

namespace App\Http\Controllers\Api;

use App\Models\Poll;
use Illuminate\Http\Request;
use App\Collections\StatusCodes;
use App\Models\PollVotingHistory;
use App\Http\Requests\VoteRequest;
use App\Http\Controllers\Controller;

class PollVotingController extends Controller
{
    //
    public function vote(VoteRequest $request){

        $validatedData = $request->validated();
        $vote = PollVotingHistory::firstWhere([
            'post_id' => $validatedData['post_id'],
            'user_id' => Auth()->user()->id,
        ]);
        if($vote){
            return response()->json([
                'status' => 'success',
                'status_code' => StatusCodes::UNPROCESSABLE,
                'message' => 'Voting already done',
                'data' => $vote
            ],StatusCodes::UNPROCESSABLE);
        }else{

            $validatedData['user_id'] = Auth()->user()->id;
            $pollVote = PollVotingHistory::create($validatedData);
            $poll = Poll::where('post_id',$pollVote->post_id)->with('votes')->get();
            return response()->json([
                'status' => 'success',
                'status_code' => StatusCodes::CREATED,
                'message' => 'Voting Completed',
                'data' => $poll 
            ],StatusCodes::CREATED);
        }
    }
}

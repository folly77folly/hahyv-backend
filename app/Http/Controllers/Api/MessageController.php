<?php

namespace App\Http\Controllers\Api;

use App\Models\Message;
use Illuminate\Http\Request;
use App\Collections\StatusCodes;
use App\Http\Controllers\Controller;
use App\Http\Requests\HistoryRequest;
use App\Http\Requests\MessageRequest;
use App\Traits\TokenTransactionsTrait;

class MessageController extends Controller
{
    use TokenTransactionsTrait;

    public function __construct(){

        $this->middleware('token', ['only' => ['store']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $id = Auth()->user()->id;
        $messages = Message::where('sender_id', $id)->with('recipient')->latest()->get();
        return response()->json([
            'status' => 'success',
            'status_code' => StatusCodes::SUCCESS,
            'message' => 'messages retrieved',
            'data' => $messages
        ],StatusCodes::SUCCESS);  
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
    public function store(MessageRequest $request)
    {
        //
        $id = Auth()->user()->id;
        $validatedData = $request->validated();
        $validatedData['sender_id'] = Auth()->user()->id;
        $message = Message::create($validatedData);
        if ($message){
            $this->debitToken($id, 1, $request->message);
        }
    return response()->json([
            'status' => 'success',
            'status_code' => StatusCodes::CREATED,
            'message' => 'message sent successfully',
            'data' => Message::find($message->id)->load('recipient')
        ],StatusCodes::CREATED);    
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
        
        $messages = Message::where([
            'sender_id'=> Auth()->user()->id,
            'recipient_id'=> $id
            ])->with('recipient')->latest()->get();
        return response()->json([
            'status' => 'success',
            'status_code' => StatusCodes::SUCCESS,
            'message' => 'messages retrieved',
            'data' => $messages
        ],StatusCodes::SUCCESS); 
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

    public function history(HistoryRequest $request){
        $validatedData = $request->validated();
        
        $messages = Message::where([
            'sender_id'=> Auth()->user()->id,
            'recipient_id'=> $request->recipient_id
            ])->with('recipient')->latest()->get();
        return response()->json([
            'status' => 'success',
            'status_code' => StatusCodes::SUCCESS,
            'message' => 'messages retrieved',
            'data' => $messages
        ],StatusCodes::SUCCESS); 
    }
}

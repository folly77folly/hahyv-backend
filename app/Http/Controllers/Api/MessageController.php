<?php

namespace App\Http\Controllers\Api;

use App\Models\Message;
use App\Events\MessageEvent;
use App\Models\Conversation;
use Illuminate\Http\Request;
use App\Collections\Constants;
use App\Collections\StatusCodes;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\HistoryRequest;
use App\Http\Requests\MessageRequest;
use App\Traits\TokenTransactionsTrait;
use App\Http\Controllers\Api\PostNotificationController;

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
        // $conversation_one = Conversation::where([
        //     'user_one' => $id,
        //     ])->get('id');
        // $conversation_two = Conversation::whereOr([
        //     'user_one' => $id,
        //     'user_two' => $id,
        //     ])->get('id');
        // $conversation_three = Conversation::whereOr([
        //     'user_one' => $id,
        //     'user_two' => $id,
        //     ])

        //     ->with(['messages' => function($query){
        //          $query->orderBy('created_at', 'asc');
        //     }])->get();
        ini_set('max_execution_time',300);
        $messages = Message::where([
            'sender_id'=> $id,
            ])->orWhere([
            'recipient_id'=> $id
            ])->with(['recipient', 'sender'])->orderBy('created_at', 'asc')->get();
        return response()->json([
            'status' => 'success',
            'status_code' => StatusCodes::SUCCESS,
            'message' => 'messages retrieved',
            'data' => $messages,
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
        $conversation_id = "";
        $id = Auth()->user()->id;
        $username = Auth()->user()->username;

        $validatedData = $request->validated();
        $validatedData['sender_id'] = Auth()->user()->id;
        $conversation_one = Conversation::where([
            'user_one' => $id,
            'user_two' => $request->recipient_id,
            ])->first();
        if (!$conversation_one){

            $conversation_two = Conversation::where([
                'user_one' => $request->recipient_id,
                'user_two' => $id,
                ])->first();

                if(!$conversation_two){

                    //create_conversation
                    $data = [
                        'user_one' => $id,
                        'user_two' => $request->recipient_id,
                    ];
                    $conversation = Conversation::create($data);
                    $conversation_id = $conversation->id;
                }else{
                    $conversation_id = $conversation_two->id;
                }
        }else{
            $conversation_id = $conversation_one->id;
        }
        $validatedData['conversation_id'] = $conversation_id;
        
        $message = Message::create($validatedData);
        if ($message){
            $reference = "msg_tk".time();
            $this->debitToken($id, 1, $request->message, $reference);
        }
        $sentMessage = Message::find($message->id)->load(['recipient', 'sender']);
        broadcast(new MessageEvent($sentMessage))->toOthers();

        //notify that you received a message
        $this->notify($username, $request->recipient_id, 'sent');
        
    return response()->json([
            'status' => 'success',
            'status_code' => StatusCodes::CREATED,
            'message' => 'message sent successfully',
            'data' => $sentMessage
        ],StatusCodes::CREATED);    
    }


    public function notify($username, $id_other_user, $type)
    {
        $post_notify = new PostNotificationController();
        $result = $post_notify->store([
            'message'=> "$username $type you a message",
            'user_id' => Auth()->user()->id,
            'broadcast_id' => $id_other_user,
            'post_type_id' => Constants::NOTIFICATION["MESSAGED"]
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //\
        $conversation_id = "";
        $conversation_one = Conversation::where([
            'user_one' => Auth()->user()->id,
            'user_two' => $id,
            ])->first();
        if(!$conversation_one){

            $conversation_two = Conversation::where([
                'user_one' => $id,
                'user_two' => Auth()->user()->id,
                ])->first();

                if(!$conversation_two){
                    //create_conversation
                    return response()->json([
                        'status' => 'success',
                        'status_code' => StatusCodes::SUCCESS,
                        'message' => 'messages retrieved',
                        'data' => []
                    ],StatusCodes::SUCCESS); 

                    
                }else{
                    $conversation_id = $conversation_two->id;
                }

        }else{
            $conversation_id = $conversation_one->id;
        }
        $messages = Message::where([
            'conversation_id'=> $conversation_id,
            ])->with(['recipient', 'sender'])->orderBy('created_at', 'asc')->get();
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
            ])->with('recipient')->orderBy('created_at', 'asc')->get();
        return response()->json([
            'status' => 'success',
            'status_code' => StatusCodes::SUCCESS,
            'message' => 'messages retrieved',
            'data' => $messages
        ],StatusCodes::SUCCESS); 
    }

    public function getConversation(int $id){
        $conversation_one ="";
        $conversation_one = Conversation::where([
            'user_one' => Auth()->user()->id,
            'user_two' => $id,
            ])->select('id as conversation_id')->first();;
        if(!$conversation_one){
            $conversation_one = Conversation::where([
                'user_one' => $id,
                'user_two' => Auth()->user()->id,
                ])->select('id as conversation_id')->first();

                if(!$conversation_one){
                    return response()->json([
                        'status' => 'failure',
                        'status_code' => StatusCodes::BAD_REQUEST,
                        'message' => 'not found',
                    ],StatusCodes::BAD_REQUEST); 
                }
            }
        return response()->json([
            'status' => 'success',
            'status_code' => StatusCodes::SUCCESS,
            'message' => 'conversation retrieved',
            'data' => $conversation_one
        ],StatusCodes::SUCCESS); 
    }

    public function getChats(){
        $id = Auth()->user()->id;
        $messages = Message::select('sender_id','recipient_id')->where([
            'sender_id'=> $id,
            ])->orWhere([
            'recipient_id'=> $id
            ])->distinct()->with(['recipient', 'sender'])->get();

        return response()->json([
            'status' => 'success',
            'status_code' => StatusCodes::SUCCESS,
            'message' => 'messages retrieved',
            'data' => $messages,
        ],StatusCodes::SUCCESS);  
    }
}

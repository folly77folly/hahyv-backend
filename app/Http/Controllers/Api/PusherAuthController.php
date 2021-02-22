<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Pusher\Pusher;

class PusherAuthController extends Controller
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
    $pusher = new Pusher('30b40ac3acc26d1a0504', 'a5496b62a6bb278a2fd0','1131331');
    $socketID = $request->socket_id;
    $channel_name = $request->channel_name;
    $signed = $pusher->socket_auth($channel_name,$socketID);
        echo($signed);
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
    public function update(Request $request)
    {
        //
        $pusher = new Pusher('30b40ac3acc26d1a0504', 'a5496b62a6bb278a2fd0','1131331');
        $socketID = $request->socket_id;
        $channel_name = $request->channel_name;
        $call_front = $request->callback;
        $signed = $pusher->socket_auth($channel_name,$socketID);
        header('Content-Type: application/javascript');
        echo($call_front . '(' . $signed . ');');
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

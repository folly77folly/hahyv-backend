<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PayStackWebhookController extends Controller
{
    //
    public function receive(Request $request){
        $result = file_get_contents('http://requestbin.net/r/911jybin');
        echo $result;
    }
}

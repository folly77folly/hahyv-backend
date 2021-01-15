<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/pusher/auth', 'Api\PusherAuthController@update');
Route::post('/pusher/auth', 'Api\PusherAuthController@store');

// Route::post('/pusher/auth', function(){
//     $pusher = new Pusher('30b40ac3acc26d1a0504', 'a5496b62a6bb278a2fd0');
//     $socketID = $_POST['socket_id'];
//     $channel_name = $_POST['channel_name'];
//     Log::$socketID ;
//     Log::$channel_name ;
//     $arr = [
//         "auth"=>"1234",
//     ];

//     return response()->json([
//         'auth'=> $channel_name
//     ]);
// });

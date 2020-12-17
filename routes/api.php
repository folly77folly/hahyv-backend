<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

//user Email Signup Route
Route::POST('/register', 'Api\AuthController@register')->name('register');
Route::POST('/login', 'Api\AuthController@login')->name('login');

//password Reset
Route::POST('/password/email', 'Api\ForgotPasswordController@sendResetLinkEmail')->name('forgot_password');
Route::POST('/password/reset', 'Api\ResetPasswordController@reset')->name('reset_password');

//Email Verification
Route::GET('email/resend', 'Api\VerificationController@resend')->name('verification.resend');
Route::GET('email/verify/{id}/{hash}', 'Api\VerificationController@verify')->name('verification.verify');
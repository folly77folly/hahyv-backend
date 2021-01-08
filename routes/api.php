<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\FollowerController;

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
// ->middleware('emailverifier');

//password Reset
Route::POST('/password/email', 'Api\ForgotPasswordController@sendResetLinkEmail')->name('forgot_password');
Route::POST('/password/reset', 'Api\ResetPasswordController@reset')->name('reset_password');

//Email Verification
Route::GET('email/resend', 'Api\VerificationController@resend')->name('verification.resend');
Route::GET('email/verify/{id}/{hash}', 'Api\VerificationController@verify')->name('verification.verify');
Route::POST('otp/verify', 'Api\VerificationController@verifyOTP')->name('verification.otp');

Route::group(['middleware'=>'auth:api'], function(){
    //all users
    Route::GET('/users', 'Api\AuthController@index')->name('allUsers');
    
    // User profile
    Route::GET('profile/preference', 'Api\UserProfileController@preference');
    Route::PUT('profile/{id}', 'Api\UserProfileController@update')->name('userProfileUpdate');
    Route::GET('profile/{id}', 'Api\UserProfileController@profile')->name('userProfile');
    // change password
    Route::POST('/changepassword', 'Api\AuthController@changePassword');
    
    // Get all preferences
    Route::GET('preferences/', 'Api\PreferenceController@index')->name('preferences');
    Route::POST('preferences/', 'Api\PreferenceController@store')->name('storePreferences');
    Route::PUT('preferences/{id}', 'Api\PreferenceController@update')->name('updatePreferences');
    Route::DELETE('preferences/{id}', 'Api\PreferenceController@destroy')->name('deletePreferences');
    Route::DELETE('user/{id}', 'Api\UserProfileController@destroy')->name('deleteUser');
    
    //follower
    Route::GET('/following', 'Api\FollowerController@following');
    Route::GET('/followers', 'Api\FollowerController@followers');
    Route::resource('follower', Api\FollowerController::class);

    Route::resource('picture', Api\PictureController::class);
    
    Route::GET('post/userspost', 'Api\PostController@usersPost');
    Route::POST('post/likepost', 'Api\PostController@likePost');
    Route::POST('post/dislikepost', 'Api\PostController@disLikePost');
    Route::POST('post/postlike', 'Api\PostController@postLike');

    Route::GET('hometimeline', 'Api\HomeTimelineController@index');

    Route::GET('card/usercards', 'Api\CardController@userCards');
    Route::PUT('card/editcard', 'Api\CardController@editCard');
    Route::DELETE('card/delete', 'Api\CardController@delete');
    
    
    Route::apiResource('post', Api\PostController::class);

    Route::apiResource('card', Api\CardController::class);

    //Comments
    Route::resource('comment', Api\CommentController::class);
    Route::POST('/comment_unlike', 'Api\CommentLikeController@comment_unlike');
    Route::resource('commentlike', Api\CommentLikeController::class);

    //Country
    Route::resource('country', Api\CountryController::class);

    //Bank Details
    Route::get('/banks', 'Api\BankDetailsController@getCommercialBanks');
    Route::post('/resolve', 'Api\BankDetailsController@resolveAccountNumber');
    Route::post('/resolves', 'Api\BankDetailsController@resolveAccount');
    Route::resource('bank_details', Api\BankDetailsController::class);

    //Bookmark
    Route::resource('bookmark', Api\BookmarkController::class);
});


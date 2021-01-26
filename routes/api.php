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
Route::POST('/login', 'Api\AuthController@login');
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
    Route::GET('post/userpost/{id}', 'Api\PostController@userPost');
    Route::POST('post/likepost', 'Api\PostController@likePost');
    Route::POST('post/dislikepost', 'Api\PostController@disLikePost');
    Route::POST('post/postlike', 'Api\PostController@postLike');
    Route::POST('post/{id}', 'Api\PostController@show');

    Route::GET('hometimeline', 'Api\HomeTimelineController@index');

    Route::GET('card/usercards', 'Api\CardController@userCards');
    Route::PUT('card/editcard/{id}', 'Api\CardController@editCard');
    Route::DELETE('card/delete/{id}', 'Api\CardController@delete');

    Route::POST('buytoken', 'Api\TokenController@buyToken');
    Route::PUT('tokenrate', 'Api\TokenController@tokenRate');
    
    //post
    Route::apiResource('post', Api\PostController::class);

    //Card
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

    //Notifications
    Route::GET('/notifications', 'Api\PostNotificationController@index');
    Route::DELETE('/notifications/{id}', 'Api\PostNotificationController@destroy');

    //payment
    Route::POST('/card_payment','Api\PaymentController@cardPayment');
    Route::POST('fund_wallet','Api\WalletController@fundWallet');
    Route::GET('wallet_transactions','Api\WalletController@index');
    Route::resource('wallet', 'Api\WalletController');

    //subscription 
    Route::POST('/subscribe_wallet', 'Api\SubscribeController@withWallet');
    Route::POST('/subscribe_card', 'Api\SubscribeController@withCard');
    
    //fans
    Route::resource('fans', Api\FanController::class);


    //transactions
    Route::GET('/card_transactions', 'Api\CardTransactionController@index');

    //Poll Vote
    Route::POST('/vote', 'Api\PollVotingController@vote');
});


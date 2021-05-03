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

//webhook
Route::webhooks('webhook-receiving-url', 'paystack');
Route::POST('/webhook-stripe-url', 'Api\WalletController@valentine');

//Admin Routes

Route::prefix('admin')->group(function(){
    Route::POST('/register', 'Api\Admin\AuthController@register');
    Route::POST('/login', 'Api\Admin\AuthController@login');
});

Route::group(['middleware'=>['auth:api','admin']], function(){

    Route::prefix('admin')->group(function(){
        // change password
        Route::POST('/change_password', 'Api\Admin\AuthController@changePassword');

        //all users
        Route::GET('/users', 'Api\Admin\DashboardController@allUsers');
        Route::GET('/users_p', 'Api\Admin\DashboardController@allUsersP');
        Route::PUT('/users', 'Api\Admin\DashboardController@deactivateUser');

        //Dashboard Counts
        Route::GET('/dashboard', 'Api\Admin\DashboardController@dashboard');

        //user profile
        Route::GET('/user/{user}', 'Api\Admin\DashboardController@profileUsername');
        Route::PUT('/subscription-expire', 'Api\Admin\DashboardController@expiry');

        //send mail
        Route::POST('/send_mail', 'Api\Admin\DashboardController@sendMail');

        //setting token rate and unit 
        Route::POST('/token_rate', 'Api\Admin\TokenController@store');
        Route::GET('/token_rate', 'Api\Admin\TokenController@index');

        Route::prefix('/send_mail')->group(function(){
            Route::POST('/user', 'Api\Admin\MessageController@user');
            Route::POST('/users', 'Api\Admin\MessageController@users');
            Route::POST('/subscribers', 'Api\Admin\MessageController@subscribers');
            Route::POST('/creators', 'Api\Admin\MessageController@creators');
        });

        //Payout to all creators
        Route::POST('payout_user', 'Api\Admin\PayoutController@payoutUser');
        Route::apiResource('payout', Api\Admin\PayoutController::class);

        //Hahyv Earnings 
        Route::apiResource('wallet_transactions', Api\Admin\HahyvEarningController::class);

        //Transaction Fee
        Route::apiResource('transaction_fee', Api\Admin\TransactionFeeController::class);

    });

});

//Admin Routes

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
    //all users for search 
    // Route::GET('/users', 'Api\AuthController@index')->name('allUsers');
    
    // User profile
    Route::GET('profile/preference', 'Api\UserProfileController@preference');
    Route::PUT('profile/{id}', 'Api\UserProfileController@update')->name('userProfileUpdate');
    Route::GET('profile/{id}', 'Api\UserProfileController@profile')->name('userProfile');
    Route::GET('profile/user/{username}', 'Api\UserProfileController@profileUsername');
    // change password
    Route::POST('/changepassword', 'Api\AuthController@changePassword');

    //Search 
    Route::GET('welcome', 'Api\AuthController@welcome');
    
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
    // Route::POST('post_new/posting', 'Api\PostController@post');

    Route::GET('hometimeline', 'Api\HomeTimelineController@index');

    Route::GET('card/usercards', 'Api\CardController@userCards');
    Route::PUT('card/editcard/{id}', 'Api\CardController@editCard');
    Route::DELETE('card/delete/{id}', 'Api\CardController@delete');

    Route::POST('buytoken', 'Api\TokenController@buyToken');
    Route::GET('token_rate', 'Api\TokenController@index');

    
    //post
    Route::apiResource('post', Api\PostController::class);

    //Card
    Route::POST('card/default', 'Api\CardController@default');
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
    Route::get('/resolve_bvn', 'Api\BankDetailsController@check');
    Route::resource('bank_details', Api\BankDetailsController::class);

    //Bookmark
    Route::resource('bookmark', Api\BookmarkController::class);

    //Notifications
    Route::GET('/notifications', 'Api\PostNotificationController@index');
    Route::DELETE('/notifications/{id}', 'Api\PostNotificationController@destroy');
    Route::DELETE('/notifications', 'Api\PostNotificationController@clearAll');
    Route::PUT('/notifications', 'Api\PostNotificationController@update');

    //payment
    Route::POST('/card_payment','Api\PaymentController@cardPayment');
    Route::POST('fund_wallet','Api\WalletController@fundWalletPayStack');
    Route::GET('wallet_transactions','Api\WalletController@index');
    Route::resource('wallet', 'Api\WalletController');

    //subscription 
    Route::POST('/subscribe_wallet', 'Api\SubscribeController@withWallet');
    Route::POST('/subscribe_card', 'Api\SubscribeController@withCard');
    Route::POST('/tip', 'Api\SubscribeController@tipWithWallet');
    Route::PUT('/unsubscribe', 'Api\SubscribeController@unsubscribe');
    
    
    //fans
    Route::resource('fans', Api\FanController::class);


    //transactions





    
    Route::GET('/card_transactions', 'Api\CardTransactionController@index');

    //Poll Vote
    Route::POST('/vote', 'Api\PollVotingController@vote')->middleware(['poll_expiry']);

    //messages
    Route::POST('/history', 'Api\MessageController@history');
    Route::GET('/conversation/{id}', 'Api\MessageController@getConversation');
    Route::GET('/chats', 'Api\MessageController@getChats')->name('getChats');
    Route::GET('/history-message/{id}', 'Api\MessageController@getHistory');
    Route::apiResource('message', Api\MessageController::class);

    //earnings
    Route::apiResource('earnings', Api\EarningController::class);

    //subscription types 
    Route::apiResource('subscription_type', Api\SubscriptionTypeController::class);

    //subscription rate 
    Route::apiResource('subscription_rate', Api\SubscriptionRateController::class);

    //Monetize Benefits 
    Route::apiResource('monetize_benefits', Api\MonetizeBenefitController::class);

    //Subscription Benefits 
    Route::apiResource('subscription_benefits', Api\SubscriptionBenefitController::class);

    //Withdrawal
    Route::POST('withdrawal', 'Api\WithdrawalRequestController@bankTransfer');
    Route::GET('withdrawal', 'Api\WithdrawalRequestController@index');
    // Route::apiResource('withdrawal', Api\WithdrawalRequestController::class);

    Route::resource('fileupload', Api\FileuploadController::class);

    //Transaction Fee
    Route::GET('transaction_fee', 'Api\TransactionFeeController@index');

    //Search 
    Route::GET('search', 'Api\UserProfileController@search');

});


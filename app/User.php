<?php

namespace App;

use App\Models\Card;
use App\Models\Like;
use App\Models\Post;
use App\Models\Crypto;
use App\Models\Comment;
use App\Models\Follower;
use App\Models\BankDetail;
use App\Models\Preference;
use Laravel\Scout\Searchable;
use App\Models\CardTransaction;
use App\Models\MonetizeBenefit;
use App\Models\SubscribersList;
use App\Models\SubscriptionRate;
use App\Models\WalletTransaction;
use App\Models\WithdrawalRequest;
use App\Traits\FollowingFanTrait;
use App\Models\EarningTransaction;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\HasApiTokens;
use App\Models\SubscriptionBenefit;
use Illuminate\Notifications\Notifiable;
use App\Notifications\PasswordNotification;
use App\Notifications\EmailVerifyNotification;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable, HasApiTokens, FollowingFanTrait, Searchable;
    public static $token;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'username',
        'otp',
        'provider_name',
        'provider_id',
        'description',
        'profile_image_url',
        'website_url',
        'gender',
        'date_of_birth',
        'is_active',
        'is_reported',
        'is_blocked',
        'followerCount',
        'followingCount',
        'postCount',
        'tokenBalance',
        'subscription_plan',
        'is_monetize',
        'subscription_amount',
        'cover_image_url',
        'rf_token',
        'referral_url',
        'ip_address',
        'is_online',
        'role_id',
        'email_verified_at'
    ];

    

    protected $appends = ['isSubscribed', 
    'unlockFee', 'pendingWithdrawal', 
    'availableEarning','allEarning','earnRate',
    'walletBalance', 'tokenBalance'];

    public function getIsSubscribedAttribute(){
        if ($this->is_monetize){
            return $this->subscribed($this->id);
        }
        return true;
    }

    public function getUnlockFeeAttribute(){
        if($this->is_monetize){
            return $this->subRate($this->id);
        }
        return 'None';
    }

    public function getPendingWithdrawalAttribute(){
        return $this->withdrawalRequests->where('approved',false)->sum('amount');
    }

    public function getEarnRateAttribute(){
        return $this->earnRate();
    }

    public function getAllEarningAttribute(){
        $result = $this->earnings->sum('amount');
        if($result > 0){

            return round($result, 2);
        }else{
            return 0;
        }
    }

    public function getAvailableEarningAttribute(){
        $available = $this->getAllEarningAttribute()  * $this->earnRate;
        return round($available,2);
    }

    public function getWalletBalanceAttribute(){
        return $this->walletValue($this->id);
    }

    public function getTokenBalanceAttribute(){
        return $this->tokenValue($this->id);
    }
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'otp_expiry', 'otp',
        'provider_id','withdrawal_requests'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_monetize' => 'boolean',
        'is_active' => 'boolean',
        'is_reported' => 'boolean',
        'theme' => 'boolean',
        'is_online' => 'boolean',
    ];

    public function sendPasswordResetNotification($token)
    {
        $affected = DB::table('users')
            ->where('id', $this->id)
            ->update(['provider_id' => $token]);
        $email = $this->email;
        $this->notify(new PasswordNotification($token, $email));
    }

    public function sendEmailVerificationNotification()
    {

        $OTP = $this->otp;
        $username = $this->username;
        $this->notify(new EmailVerifyNotification($OTP, $username));
    }

    public function follower()
    {
        return $this->hasMany(Follower::class);
    }

    public function following()
    {
        return $this->hasMany(Follower::class, 'following_userId');
    }


    public function preference()
    {
        return $this->belongsTo(Preference::class);
    }

    public function post() 
    {
        return $this->hasMany(Post::class);
    }
    public function like() 
    {
        return $this->hasMany(Like::class);
    }

    //relationship between user and comment
    public function comment() 
    {
        return $this->hasMany(Comment::class);
    }
    
    public function card() 
    {
        return $this->hasMany(Card::class);
    }
    
    public function crypto() 
    {
        return $this->hasMany(Crypto::class);
    }

    //users subscribed to me
    public function subscribers()
    {
        return $this->hasMany(SubscribersList::class, 'creator_id');
    }

    // monetization benefits
    public function monetizeBenefits()
    {
        return $this->hasMany(MonetizeBenefit::class);
    }

    // subscription benefits
    public function subscriptionBenefits()
    {
        return $this->hasMany(SubscriptionBenefit::class);
    }

    // subscription rates
    public function subscriptionRates()
    {
        return $this->hasMany(SubscriptionRate::class);
    }

    // Bank Details 
    public function bankDetail()
    {
        return $this->hasOne(BankDetail::class);
    }

    // Withdrawal Requests 
    public function withdrawalRequests()
    {
        return $this->hasMany(WithdrawalRequest::class);
    }

    // All Earnings 
    public function earnings()
    {
        return $this->hasMany(EarningTransaction::class);
    }

    // All Wallet 
    // public function wallet()
    // {
    //     return $this->hasMany(WalletTransaction::class);
    // }

    // All Earnings 
    public function cardTrans()
    {
        return $this->hasMany(CardTransaction::class, 'user');
    }

    public function searchableAs(){
        return 'users_username';
    }
    /**
     * Modify the query used to retrieve models when making all of the models searchable.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function makeAllSearchableUsing($query)
    {
        $users = DB::table('users')->where('role_id', 2)->get();
        return $query;
    }

    public function shouldBeSearchable()
    {
        return $this->role_id == 2;
    }

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        $array = $this->username;
        $array = $this->toArray();

        // Customize the data array...

        return $array;
    }
}

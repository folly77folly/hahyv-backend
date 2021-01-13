<?php

namespace App;

use App\Models\Crypto;
use App\Models\Card;
use App\Models\Like;
use App\Models\Post;
use App\Models\Comment;
use App\Models\Follower;
use App\Models\Preference;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use App\Notifications\PasswordNotification;
use App\Notifications\EmailVerifyNotification;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable, HasApiTokens;
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
        'cover_image_url'
    ];

    public function getFollowingAttribute(){
        
    }

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'otp_expiry', 'otp',
        'provider_id'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
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
}

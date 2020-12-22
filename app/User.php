<?php

namespace App;

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
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'otp_expiry'
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
        $this->notify(new PasswordNotification($token));
        
    }

    public function sendEmailVerificationNotification()
    {

        $OTP = $this->otp;
        $username = $this->username;
        $this->notify(new EmailVerifyNotification($OTP, $username));
    }
}

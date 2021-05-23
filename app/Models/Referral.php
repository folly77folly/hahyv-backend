<?php

namespace App\Models;

use App\User;
use App\Traits\ReferralTrait;
use Illuminate\Database\Eloquent\Model;

class Referral extends Model
{
    use ReferralTrait;
    //
    protected $guarded =[];
    protected $hidden = [
        'user_id',
        'referred_id'
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function referredUser()
    {
        return $this->belongsTo(User::class, 'referred_id');
    }

}

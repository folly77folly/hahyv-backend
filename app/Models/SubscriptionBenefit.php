<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class SubscriptionBenefit extends Model
{
    //
    protected $guarded =[];

    public function subscriptionType()
    {
        return $this->belongsTo(User::class);
    }
}

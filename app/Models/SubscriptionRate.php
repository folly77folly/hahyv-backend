<?php

namespace App\Models;

use App\Models\SubscriptionType;
use Illuminate\Database\Eloquent\Model;

class SubscriptionRate extends Model
{
    //
    protected $guarded =[];

    public function subscription()
    {
        return $this->belongsTo(SubscriptionType::class, 'subscription_type_id');
    }
}

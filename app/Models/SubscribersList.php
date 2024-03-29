<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class SubscribersList extends Model
{
    //
    protected$guarded =[];

    protected $casts = [
        'expiry' => 'datetime',
        'is_active' => 'boolean'
    ];
    
    public function user(){
        return $this->belongsTo(User::class);
    }

    public function creator(){
        return $this->belongsTo(User::class, 'creator_id');
    }
}

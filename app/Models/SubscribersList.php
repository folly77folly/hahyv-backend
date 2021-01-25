<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscribersList extends Model
{
    //
    protected$guarded =[];
    
    public function user(){
        return $this->belongsTo(User::class);
    }

    public function creator(){
        return $this->belongsTo(User::class, 'creator_id');
    }
}

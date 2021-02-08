<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    //
    protected $guarded = [];

    public function sender(){
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function recipient(){
        return $this->belongsTo(User::class, 'recipient_id');
    }
}

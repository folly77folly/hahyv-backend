<?php

namespace App\Models;

use App\User;
use App\Models\Message;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    //
    protected $guarded =[];

    public function messages()
    {
       return $this->hasMany(Message::class);
    }

    public function user_one(){
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function user_two(){
        return $this->belongsTo(User::class, 'recipient_id');
    }
}

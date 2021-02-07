<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    //
    protected $fillable = [
        'sender_id',
        'recipient_id',
        'message'
    ];

    public function sender(){
        return $this->hasMany(User::class, 'id');
    }

    public function recipient(){
        return $this->hasMany(User::class, 'id');
    }
}

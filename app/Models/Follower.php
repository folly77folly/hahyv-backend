<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Follower extends Model
{
    //
    protected $fillable = [
        'user_id',
        'following_userId'
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function following(){
        return $this->belongsTo(User::class, 'following_userId');
    }
}

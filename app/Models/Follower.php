<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Follower extends Model
{
    //
    protected $fillable = [
        'user_id',
        'following_userID'
    ];

    public function users(){
        return $this->hasMany(User::class);
    }
}

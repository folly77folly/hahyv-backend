<?php

namespace App\Models;

use App\User;
use App\Models\Poll;
use Illuminate\Database\Eloquent\Model;

class PollVotingHistory extends Model
{
    //
    protected $guarded = [];

    public function post(){
        return $this->belongsTo(Post::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function poll(){
        return $this->belongsTo(Poll::class);
    }
}

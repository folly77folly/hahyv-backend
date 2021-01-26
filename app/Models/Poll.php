<?php

namespace App\Models;

use App\Models\Post;
use App\Models\PollVotingHistory;
use Illuminate\Database\Eloquent\Model;

class Poll extends Model
{
    //
    protected $guarded = [];

    public function post(){
        return $this->belongsTo(Post::class);
    }

    public function votes(){
        return $this->hasMany(PollVotingHistory::class);
    }
}

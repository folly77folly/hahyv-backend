<?php

namespace App\Models;

use App\User;
use App\Models\Like;
use App\Models\Poll;
use App\Models\Comment;
use App\Providers\Following;
use App\Traits\FollowingFanTrait;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use FollowingFanTrait;
    
    protected $fillable = [
        'description',
        'images',
        'videos',
        'poll',
        'user_id',
        'likesCount',
        'dislikesCount',
        'name',
        'username'
    ];

    protected $casts = [
        'images' =>'array',
        'videos' =>'array',
        'poll' =>'array',
        'disable_comment' => 'boolean',
        'accept_tip' => 'boolean',
        'is_paid' => 'boolean',
    ];
    protected $appends = array('canComment');

    public function getCanCommentAttribute(){
        return $this->check($this->user_id);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comment()
    {
        return $this->hasMany(Comment::class);
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function polls()
    {
        return $this->hasMany(Poll::class);
    }
}
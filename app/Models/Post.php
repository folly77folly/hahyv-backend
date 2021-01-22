<?php

namespace App\Models;

use App\User;
use App\Models\Like;
use App\Models\Comment;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
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
        'disable_comment' => 'boolean',
        'accept_tip' => 'boolean',
        'is_paid' => 'boolean',
    ];

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
}
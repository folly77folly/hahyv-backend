<?php

namespace App\Models;

use App\User;
use App\Models\Post;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    //
    use SoftDeletes;
    protected $fillable = [
        'user_id',
        'post_id',
        'comment',
        'picture',
        'video',
    ];

    protected $casts = [
        'picture' => 'array',
        'video' => 'array' 
    ];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}

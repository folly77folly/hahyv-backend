<?php

namespace App\Models;

use App\User;
use App\Models\Post;
use Illuminate\Database\Eloquent\Model;

class PostNotification extends Model
{
    //
    protected $fillable = [
        'message',
        'post_id',
        'user_id',
    ];

    protected $casts = [
        'read' => 'boolean'
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }
    public function post(){
        return $this->belongsTo(Post::class);
    }
}

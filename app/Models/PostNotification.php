<?php

namespace App\Models;

use App\User;
use App\Models\Post;
use App\Models\PostType;
use Illuminate\Database\Eloquent\Model;

class PostNotification extends Model
{
    //
    protected $fillable = [
        'message',
        'post_id',
        'user_id',
        'post_type_id',
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
    public function post_type(){
        return $this->belongsTo(PostType::class);
    }
}

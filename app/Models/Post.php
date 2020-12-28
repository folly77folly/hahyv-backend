<?php

namespace App\Models;

use App\User;
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
        'dislikesCount'
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

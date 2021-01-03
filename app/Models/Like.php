<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    protected $fillable = [
        'user_id',
        'liking_userId',
        'post_id',
        'liked'
    ];

    protected $casts = [
        'liked' => 'boolean'
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

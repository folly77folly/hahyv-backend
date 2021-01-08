<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bookmark extends Model
{
    //
    protected $fillable = [
        'user_id',
        'post_id'
    ];
    
    protected $casts = [
        'status'=>'boolean'
    ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function post()
    {
        return $this->belongsTo('App\Models\Post');
    }
}

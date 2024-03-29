<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommentLike extends Model
{
    //
    protected $fillable = [
        'user_id',
        'comment_id',
        'liked'
    ];

    protected $casts = [
        'liked' => 'boolean'
     ];

     public function user(){
         return $this->belongsTo('App\User');
     }
}

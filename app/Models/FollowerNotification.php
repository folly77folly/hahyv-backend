<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FollowerNotification extends Model
{
    //
    protected $fillable = [
        'message',
        'user_id',
        'post_type_id',
    ];


    protected $casts = [
        'read' => 'boolean'
    ];
}

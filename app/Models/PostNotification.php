<?php

namespace App\Models;

use App\User;
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
}

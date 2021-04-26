<?php

namespace App\Models;

use App\Models\PostNotification;
use Illuminate\Database\Eloquent\Model;

class PostType extends Model
{
    //
    public function post_notification(){
        return $this->hasMany(PostNotification::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Picture extends Model
{
    //
    protected $fillable = [
        'user_id',
        'title',
        'photos',
        'photoCount',
        'photoTag'
    ];
}

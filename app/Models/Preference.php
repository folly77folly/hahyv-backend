<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Preference extends Model
{
    //
    protected $fillable = [
        'preference'
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}

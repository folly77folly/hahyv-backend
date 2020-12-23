<?php

namespace App;

use App\User;
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

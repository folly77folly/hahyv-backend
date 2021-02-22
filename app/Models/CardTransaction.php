<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class CardTransaction extends Model
{
    //
    protected $guarded =[];

    protected $hidden = [
        'receipt_no'
    ];

    public function earningUser(){
        return $this->belongsTo(User::class);
    }
}

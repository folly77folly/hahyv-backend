<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CardTransaction extends Model
{
    //
    protected $guarded =[];

    protected $hidden = [
        'receipt_no'
    ];
}

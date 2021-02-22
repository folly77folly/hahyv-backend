<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionFee extends Model
{
    //
    protected $guarded =[];

    protected $casts =  [
        'status' => 'boolean'
    ];
}

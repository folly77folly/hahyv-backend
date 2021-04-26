<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class WithdrawalRequest extends Model
{
    //
    protected $guarded = [];

    protected $casts = [
        'approved' => 'boolean',
    ];

    public function user(){

        return $this->belongsTo(User::class);
    }


}

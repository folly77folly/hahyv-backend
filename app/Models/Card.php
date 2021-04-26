<?php

namespace App\models;

use App\User;
use Illuminate\Database\Eloquent\Model;


class Card extends Model
{

    protected $fillable = [
        'user_id',
        'cardName',
        'cardNo',
        'cardExpiringMonth',
        'cardExpiringYear',
        'cardCVV',
        'account_name'
    ];

    protected $casts = [
        'default' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

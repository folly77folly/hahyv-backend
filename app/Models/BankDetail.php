<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankDetail extends Model
{
    //
    protected $fillable =[
        'user_id',
        'bank_id',
        'bank_name',
        'first_name',
        'last_name',
        'account_no',
        'account_name',
        'address',
        'bvn',
        'phone_no',
        'zip_code',
        'country_id',
        'instagram',
        'twitter',
        'date_of_birth',
        'identification_image',
        'identification_exp_date'
    ];

    protected $casts = [
        'date_of_birth' =>'date',
        'identification_exp_date' => 'date'
    ];

    public function user(){
        return $this->belongsTo('App\User');
    }
    public function country(){
        return $this->belongsTo('App\Models\Country');
    }
}

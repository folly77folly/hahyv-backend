<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EarningTransaction extends Model
{
    //
    protected $guarded =[];

    public function type(){
        
        return $this->belongsTo(Type::class);
    }
}

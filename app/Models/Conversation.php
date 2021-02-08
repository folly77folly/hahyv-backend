<?php

namespace App\Models;

use App\Models\Message;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    //
    protected $guarded =[];

    public function messages()
    {
        $this->hasMany(Message::class);
    }
}

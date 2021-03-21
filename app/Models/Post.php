<?php

namespace App\Models;

use App\User;
use App\Models\Like;
use App\Models\Poll;
use App\Models\Comment;
use App\Providers\Following;
use App\Traits\FollowingFanTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Post extends Model
{
    use FollowingFanTrait;
    
    protected $guarded =[];

    protected $casts = [
        'images' =>'array',
        'videos' =>'array',
        'poll' =>'array',
        'disable_comment' => 'boolean',
        'accept_tip' => 'boolean',
        'is_paid' => 'boolean',
    ];
    protected $appends = array('canComment', 'is_bookmark', 'showTip','url', 'uploaded_time');

    public function getUrlAttribute()
    {
        return Storage::disk('s3')->url($this->path);
    }

    public function getUploadedTimeAttribute()
    {
        return $this->created_at->diffForHumans();
    }



    public function getCanCommentAttribute(){
        return $this->check($this->user_id);
    }

    public function getIsBookmarkAttribute(){

        return $this->bookmark($this->id);
    }

    public function getShowTipAttribute(){

        if ($this->user_id == Auth()->user()->id){
            return false;
        }

        if(count($this->images) > 0 || count($this->videos)> 0 ){
            
            if ($this->user->is_monetize == false){
                return true;
            }
            if ($this->user->is_monetize == true && $this->accept_tip){
                return true;
            }
        }

        return false;
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comment()
    {
        return $this->hasMany(Comment::class);
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function polls()
    {
        return $this->hasMany(Poll::class);
    }
}
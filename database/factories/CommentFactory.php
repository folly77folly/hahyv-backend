<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\User;
use App\Models\Post;
use App\Models\Comment;
use Faker\Generator as Faker;

$factory->define(Comment::class, function (Faker $faker) {
    return [
        //
        'user_id' => function(){
            return factory(User::class)->create()->id;
        },
        'post_id' => function(){
            return factory(Post::class)->create()->id;
        },
        'comment' => $faker->sentence(15),
        'picture' => ["1.png"],
        'video' => ["2.mp4"]
    ];
});

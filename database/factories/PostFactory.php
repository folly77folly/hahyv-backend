<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Post;
use App\User;
use Faker\Generator as Faker;

$factory->define(Post::class, function (Faker $faker) {
    return [
        //
        'user_id' => function(){
            return factory(User::class)->create()->id;
        },
        'description' => $faker->sentence(10),
        'images' => ["abc.png", "def.png"],
        'videos' => ["abc.mp4", "def.mp4"],
        'poll' => $faker->name,
        'likesCount' => $faker->randomDigit,
        'dislikesCount' => $faker->randomDigit,
    ];
});

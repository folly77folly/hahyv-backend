<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


class FollowerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::table('followers')->insert([
            'user_id' => rand(1, 31),
            'following_userId' => rand(1, 31),
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}

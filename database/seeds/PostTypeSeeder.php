<?php

use App\Models\PostType;
use Illuminate\Database\Seeder;

class PostTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $post_type1 = new PostType();
        $post_type1->name = 'liked';
        $post_type1->save();

        $post_type2 = new PostType();
        $post_type2->name = 'subscribed';
        $post_type2->save();

        $post_type3 = new PostType();
        $post_type3->name = 'tipped';
        $post_type3->save();

        $post_type4 = new PostType();
        $post_type4->name = 'message';
        $post_type4->save();

        $post_type5 = new PostType();
        $post_type5->name = 'follow';
        $post_type5->save();
    }
}

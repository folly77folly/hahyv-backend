<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnPosterToPostNotifictionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('post_notifications', function (Blueprint $table) {
            //
            $table->foreignId('broadcast_id')->after('user_id')->references('id')->on('users')->onDelete('cascade');        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('post_notifications', function (Blueprint $table) {
            //
            $table->dropColumn('broadcast_id');
        });
    }
}

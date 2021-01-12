<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFollowerNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('follower_notifications', function (Blueprint $table) {
            $table->id();
            $table->string('message');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('post_type_id')->constrained()->onDelete('cascade');
            $table->boolean('read')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('follower_notifications');
    }
}

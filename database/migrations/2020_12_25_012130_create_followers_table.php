<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFollowersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('followers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('following_userId');
            $table->timestamps();
        });

        Schema::table('followers', function(Blueprint $table){
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('following_userId')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();
        Schema::table('followers', function(Blueprint $table){
            
            $table->dropIfExists('user_id');
            $table->dropIfExists('following_userId');
        });
        // Schema::dropIfExists('user_id');
        // Schema::dropIfExists('following_userId');
        Schema::enableForeignKeyConstraints();
        Schema::dropIfExists('followers');
    }
}

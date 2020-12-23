<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMoreFieldsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->text('description')->nullable();
            $table->string('profile_image_url')->nullable();
            $table->string('preference')->nullable();
            $table->string('website_url')->nullable();
            $table->string('gender')->nullable();
            $table->string('date_of_birth')->nullable();
            $table->boolean('is_active')->default(1);
            $table->boolean('is_reported')->default(0);
            $table->boolean('is_blocked')->default(0);
            $table->bigInteger('followerCount')->default(0);
            $table->bigInteger('followingCount')->default(0);
            $table->bigInteger('fansCount')->default(0);
            $table->bigInteger('PostCount')->default(0);
            $table->bigInteger('walletBalance')->default(0);
            $table->bigInteger('tokenBalance')->default(0);
            $table->string('subscription_plan')->nullable();
            $table->boolean('is_monetize')->default(0);
            $table->bigInteger('subscription_amount')->default(0);
        });
    }




    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('Users', function (Blueprint $table) {
            //
        });
    }
}

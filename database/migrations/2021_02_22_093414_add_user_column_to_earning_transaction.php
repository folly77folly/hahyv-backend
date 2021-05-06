<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserColumnToEarningTransaction extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::table('earning_transactions', function (Blueprint $table) {
            //
            $table->string('trans_id')->after('type_id')->nullable()->unique();
            $table->unsignedBigInteger('earning_type_id')->after('type_id')->nullable();
            $table->unsignedBigInteger('sender_id')->after('earning_type_id')->nullable();
        });

        Schema::table('earning_transactions', function (Blueprint $table) {
            //
            $table->foreign('sender_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('earning_type_id')->references('id')->on('earning_types')->onDelete('cascade');
        });
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {   Schema::disableForeignKeyConstraints();
        Schema::table('earning_transactions', function (Blueprint $table) {
            //
            $table->dropIfExists('trans_id');
            // $table->dropForeign('sender_id');
            $table->dropIfExists('sender_id');
            // $table->dropForeign('earning_type_id');
            $table->dropIfExists('earning_type_id');
        });
        Schema::enableForeignKeyConstraints();
    }
}

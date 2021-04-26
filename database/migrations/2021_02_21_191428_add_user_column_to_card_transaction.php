<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserColumnToCardTransaction extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('card_transactions', function (Blueprint $table) {
            //
            $table->integer('trans_type')->after('card_details')->nullable();
            $table->unsignedBigInteger('user')->after('trans_type')->nullable();
        });

        Schema::table('card_transactions', function (Blueprint $table) {
            //
            $table->foreign('user')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('card_transactions', function (Blueprint $table) {
            //
            $table->dropColumn('trans_type');
            $table->dropColumn('debit_user');
        });
    }
}

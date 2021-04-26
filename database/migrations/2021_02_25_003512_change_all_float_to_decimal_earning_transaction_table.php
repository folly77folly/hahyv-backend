<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeAllFloatToDecimalEarningTransactionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('earning_transactions', function (Blueprint $table) {
            //
            $table->decimal('amount',19,2)->change();
        });

        Schema::table('wallet_transactions', function (Blueprint $table) {
            //
            $table->decimal('previousWalletBalance',19,2)->unsigned()->change();
            $table->decimal('presentWalletBalance',19,2)->unsigned()->change();
            $table->decimal('amountCredited',19,2)->unsigned()->change();
            $table->decimal('amountDebited',19,2)->unsigned()->change();
        });

        Schema::table('card_transactions', function (Blueprint $table) {
            //
            $table->decimal('amount',19,2)->change();
        });

        Schema::table('token_transactions', function (Blueprint $table) {
            //
            $table->decimal('previousTokenBalance',19,2)->unsigned()->change();
            $table->decimal('presentTokenBalance',19,2)->unsigned()->change();
            $table->decimal('tokenCredited',19,2)->unsigned()->change();
            $table->decimal('tokenDebited',19,2)->unsigned()->change();
        });

        Schema::table('hahyv_earnings', function (Blueprint $table) {
            //
            $table->decimal('amount',19,2)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('earning_transactions', function (Blueprint $table) {
            //
        });
    }
}

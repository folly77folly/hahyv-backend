<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDescriptionColumnToTokenTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('token_transactions', function (Blueprint $table) {
            //
            $table->longText('description')->after('user_id')->nullable();
            $table->float('previousTokenBalance',8,2)->change();
            $table->float('presentTokenBalance',8,2)->change();
            $table->float('tokenCredited',8,2)->change();
            $table->float('tokenDebited',8,2)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('token_transactions', function (Blueprint $table) {
            //
            $table->dropColumn('description');
        });
    }
}

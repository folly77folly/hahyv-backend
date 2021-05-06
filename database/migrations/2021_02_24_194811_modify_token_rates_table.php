<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyTokenRatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tokenrates', function (Blueprint $table) {
            //
            $table->renameColumn('dollartokenrate', 'rate')->change();
            $table->integer('unit')->after('id')->unsigned();
        });

        Schema::table('tokenrates', function (Blueprint $table) {
            //
            $table->float('rate',8,2)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tokenrates', function (Blueprint $table) {
            //
            $table->dropColumn('unit');
            $table->renameColumn('rate', 'dollartokenrate')->change();
        });

        Schema::table('tokenrates', function (Blueprint $table) {
            //
            $table->integer('dollartokenrate')->change();
        });
    }
}

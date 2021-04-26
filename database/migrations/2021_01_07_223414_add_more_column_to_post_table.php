<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMoreColumnToPostTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('posts', function (Blueprint $table) {
            //
            $table->float('height',8, 2)->after('dislikesCount')->unsigned()->nullable();
            $table->float('width',8, 2)->after('height')->unsigned()->nullable();
            $table->enum('orientation',['landscape', 'portrait'])->after('width');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('posts', function (Blueprint $table) {
            //
            $table->dropColumn('height');
            $table->dropColumn('width');
            $table->dropColumn('orientation');
        });
    }
}

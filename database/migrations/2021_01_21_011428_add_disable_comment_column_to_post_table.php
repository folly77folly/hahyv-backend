<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDisableCommentColumnToPostTable extends Migration
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
            $table->boolean('disable_comment')->after('orientation')->default(0);
            $table->boolean('accept_tip')->after('disable_comment')->default(0);
            $table->boolean('is_paid')->after('accept_tip')->default(0);
            $table->decimal('price', 8, 2)->after('is_paid')->default(0.00);
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
            $table->dropColumn('disable_comment');
            $table->dropColumn('accept_tip');
            $table->dropColumn('is_paid');
            $table->dropColumn('price');
        });
    }
}

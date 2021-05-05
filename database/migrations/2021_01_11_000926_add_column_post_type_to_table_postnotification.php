<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnPostTypeToTablePostnotification extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('post_notifications', function (Blueprint $table) {
            //
            $table->foreignId('post_type_id')->constrained()->afterColumn('post_id')->OnDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('post_notifications', function (Blueprint $table) {
            //
            $table->dropIfExists('post_type_id');
        });
    }
}

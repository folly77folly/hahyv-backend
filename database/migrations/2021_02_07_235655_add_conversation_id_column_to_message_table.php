<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddConversationIdColumnToMessageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('messages', function (Blueprint $table) {
            //
            $table->unsignedBigInteger('conversation_id')->after('message');
            $table->boolean('status')->after('message')->default(0);
            $table->softDeletes();
        });

        Schema::table('messages', function (Blueprint $table) {
            //
            $table->foreign('conversation_id')->references('id')->on('conversations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('messages', function (Blueprint $table) {
            //
            $table->dropIfExists('conversation_id');
            $table->dropIfExists('status');
            $table->dropIfExists('deleted_at');
        });
    }
}

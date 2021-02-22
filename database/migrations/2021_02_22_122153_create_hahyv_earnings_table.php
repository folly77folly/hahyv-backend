<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHahyvEarningsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hahyv_earnings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_id')->constraint('users')->onDelete('cascade');
            $table->foreignId('receiver_id')->constraint('users')->onDelete('cascade');
            $table->string('description',225)->nullable();
            $table->double('amount',8,2);
            $table->foreignId('type_id')->constraint()->onDelete('cascade');
            $table->string('trans_id',225)->nullable();
            $table->foreignId('earning_type_id')->constraint()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hahyv_earnings');
    }
}

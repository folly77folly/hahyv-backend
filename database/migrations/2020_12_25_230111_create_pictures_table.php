<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePicturesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pictures', function (Blueprint $table) {
            //
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->string('title');
            $table->longText('photos');
            $table->integer('photoCount');
            $table->string('photoTag', 255);
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
        Schema::table('pictures', function (Blueprint $table) {
            //
            $table->dropIfExists('pictures');
        });
    }
}

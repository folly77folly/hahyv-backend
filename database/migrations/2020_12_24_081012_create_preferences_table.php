<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePreferencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::dropIfExists('preferences');
        Schema::create('preferences', function (Blueprint $table) {
            $table->id();
            $table->string('preference')->unique();
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
        Schema::table('users', function (Blueprint $table) {
            $table->dropIfExists('preference');
        });

        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('preferences');
        Schema::enableForeignKeyConstraints();
    }
}

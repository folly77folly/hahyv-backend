<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBankDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bank_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('bank_id');
            $table->string('bank_name');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('account_no');
            $table->string('account_name');
            $table->string('bvn')->nullable();
            $table->string('phone_no')->nullable();
            $table->string('zip_code')->nullable();
            $table->foreignId('country_id')->constrained()->onDelete('cascade');
            $table->string('instagram')->nullable();
            $table->string('twitter')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('identification_image');
            $table->date('identification_exp_date');
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
        Schema::dropIfExists('bank_details');
    }
}

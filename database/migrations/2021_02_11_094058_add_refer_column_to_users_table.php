<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddReferColumnToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        Schema::disableForeignKeyConstraints();
        Schema::table('users', function (Blueprint $table) {
            //
            $table->ipAddress('ip_address')->nullable();
            $table->uuid('rf_token')->nullable();
            $table->string('referral_url',255)->nullable();
            $table->foreignId('role_id')->default(2)->constrained()->onDelete('cascade');
        });
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::disableForeignKeyConstraints();
        Schema::table('users', function (Blueprint $table) {
            //
            $table->dropColumn('ip_address');
            $table->dropColumn('rf_token');
            $table->dropColumn('referral_url');
            $table->dropForeign(['role_id']);
            $table->dropColumn('role_id');
        });
        // Schema::enableForeignKeyConstraints();
    }
}

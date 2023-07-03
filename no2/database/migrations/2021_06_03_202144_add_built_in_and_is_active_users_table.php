<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBuiltInAndIsActiveUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->unique();
            $table->dateTime('phone_verified_at')->nullable();
            $table->string('token_firebase', 255)->nullable()->default(NULL);
            $table->boolean('is_active')->default(0);
            $table->boolean('built_in')->default(0);
            $table->dateTime('last_signedin')->nullable();
            $table->dateTime('last_access')->nullable();
            $table->dateTime('last_update_location')->nullable();
            $table->double('latitude')->default(0.0);
            $table->double('longitude')->default(0.0);
            $table->string('device_info')->nullable()->default(NULL);
            

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        
    }
}

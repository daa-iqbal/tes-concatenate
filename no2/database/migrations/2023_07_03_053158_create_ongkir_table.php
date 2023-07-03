<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOngkirTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ongkir', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('origin_id')->nullable();
            $table->bigInteger('destination_id')->nullable();
            $table->bigInteger('courier_id')->nullable();
            $table->integer('weight')->nullable();
            $table->string('service', 255)->nullable();
            $table->text('description')->nullable();
            $table->bigInteger('cost_value')->nullable();
            $table->string('cost_etd',255)->nullable();
            $table->text('cost_note')->nullable();
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->dateTime('deleted_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ongkir');
    }
}

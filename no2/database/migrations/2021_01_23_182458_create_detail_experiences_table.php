<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetailExperiencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detail_experiences', function (Blueprint $table) {
            $table->increments('id');         
            $table->morphs('paket');
            $table->unsignedInteger('urutan')->nullable();
            $table->unsignedBigInteger('image_file_id')->nullable();
            $table->string('deskripsi')->nullable();
            $table->timestamps();
            $table->foreign('image_file_id')->references('id')->on('files')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('detail_experiences');
    }
}

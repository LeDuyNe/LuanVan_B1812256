<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableUserimagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('userimages', function (Blueprint $table) {
            $table->increments('id');
            $table->string('userImagesId')->unique()->primary();
            $table->string('url');
            $table->binary('image');

            $table->foreign('userImagesId')->references('userId')->on('users')->onDelete('cascade');;

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
        Schema::dropIfExists('userimages');
    }
}

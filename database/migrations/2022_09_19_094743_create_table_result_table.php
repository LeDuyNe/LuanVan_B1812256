<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableResultTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('result', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->integer('score')->nullable();
            $table->integer('restTime')->nullable();
            $table->uuid('examineeId');
            $table->uuid('emxamId');
            $table->foreign('examineeId')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('emxamId')->references('id')->on('exams')->onDelete('cascade');

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
        Schema::dropIfExists('result');
    }
}
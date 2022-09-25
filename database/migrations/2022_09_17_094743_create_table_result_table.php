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
            $table->increments('id');
            $table->string('resultId')->unique()->primary();
            $table->string('examineeId');
            $table->string('emxamId');
            $table->integer('score')->nullable();
            $table->integer('restTime')->nullable();

            $table->foreign('examineeId')->references('userId')->on('users')->onDelete('cascade');
            $table->foreign('emxamId')->references('emxamId')->on('exams')->onDelete('cascade');

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

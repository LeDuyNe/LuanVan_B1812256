<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
            $table->integer('restTime')->nullable();
            $table->integer('chooseAnswer')->unsigned();
            $table->integer('examineeId')->unsigned();;
            $table->integer('emxamId')->unsigned();;
            
            $table->foreign('chooseAnswer')->references('id')->on('questionBank_question')->onDelete('cascade');;
            $table->foreign('examineeId')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('emxamId')->references('id')->on('exams')->onDelete('cascade');

            $table->uuid('uuid')->unique();
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

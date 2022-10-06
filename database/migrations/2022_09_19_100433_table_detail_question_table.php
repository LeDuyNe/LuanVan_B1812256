<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TableDetailQuestionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detail_question', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('content');
            $table->boolean('isCorrect');
            $table->uuid('quesitonId');
            $table->timestamps();

            $table->foreign('quesitonId')->references('id')->on('questions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}

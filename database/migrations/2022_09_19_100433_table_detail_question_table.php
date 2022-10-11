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
            $table->longText('content');
            $table->boolean('isCorrect');
            $table->uuid('questionId');
            $table->timestamps();

            $table->foreign('questionId')->references('id')->on('questions')->onDelete('cascade');
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

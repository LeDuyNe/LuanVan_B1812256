<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TableAnswerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('answer', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('answerId');
            $table->uuid('resultId');
            $table->timestamps();

            $table->foreign('answerId')->references('id')->on('detail_question')->onDelete('cascade');
            $table->foreign('resultId')->references('id')->on('result')->onDelete('cascade');
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

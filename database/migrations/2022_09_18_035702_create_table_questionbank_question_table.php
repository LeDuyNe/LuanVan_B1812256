<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateTableQuestionbankQuestionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('questionBank_question', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('questionBankId')->unsigned();
            $table->integer('quesitonId')->unsigned();
            $table->unique(['questionBankId', 'quesitonId']);
            $table->uuid('uuid')->unique();
            $table->timestamps();

            $table->foreign('questionBankId')->references('id')->on('questionBank')->onDelete('cascade');
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
        Schema::dropIfExists('table_questionbank_question');
    }
}

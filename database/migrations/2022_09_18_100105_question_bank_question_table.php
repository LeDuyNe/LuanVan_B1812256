<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class QuestionBankQuestionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        {
            Schema::create('questionBank_questions', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('questionBankId');
                $table->uuid('quesitonId');
                $table->unique(['questionBankId', 'quesitonId']);
                $table->timestamps();
    
                $table->foreign('questionBankId')->references('id')->on('questionBank')->onDelete('cascade');
                $table->foreign('quesitonId')->references('id')->on('questions')->onDelete('cascade');
    
            });
        }
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

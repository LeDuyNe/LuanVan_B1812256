<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableQuestionBankQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    { {
            Schema::create('questionbank_questions', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('questionBankId');
                $table->uuid('questionId');
                $table->unique(['questionBankId', 'questionId']);
                $table->timestamps();
                $table->foreign('questionBankId')->references('id')->on('questionbank')->onDelete('cascade');
                $table->foreign('questionId')->references('id')->on('questions')->onDelete('cascade');
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
        Schema::dropIfExists('table__question_bank__questions');
    }
}

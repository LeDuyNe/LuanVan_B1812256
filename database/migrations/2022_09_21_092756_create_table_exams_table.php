<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateTableExamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exams', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string("arrayQuestion");
            $table->integer('timeDuration');
            $table->string('timeStart');
            $table->integer('countLimit');
            $table->string('note')->nullable();
            $table->boolean('isPublished')->default(0);  
            $table->integer('questionBank_question_id')->unsigned();
            $table->integer('creatorId')->unsigned();
            $table->timestamps();
            $table->uuid('uuid')->unique();
            
            $table->foreign('questionBank_question_id')->references('id')->on('questionBank_question')->onDelete('cascade');
            $table->foreign('creatorId')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('examinfos');
    }
}

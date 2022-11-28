<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
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
            $table->uuid('id')->primary();  
            $table->string('name');
            $table->longText("arrayQuestion");
            $table->integer('timeDuration');
            $table->string('timeStart');
            $table->integer('countLimit');
            $table->string('numExamination')->nullable();
            $table->boolean('isPublished')->default(0);  
            $table->string('note')->nullable();
            $table->uuid('questionBankId');
            $table->uuid('creatorId');
            
            $table->timestamps();
            
            $table->foreign('questionBankId')->references('id')->on('questionbank')->onDelete('cascade');
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
        // Schema::dropIfExists('examinfos');
    }
}

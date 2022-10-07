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
            $table->string("arrayQuestion");
            $table->integer('timeDuration');
            $table->string('timeStart');
            $table->integer('countLimit');
            $table->string('note')->nullable();
            $table->boolean('isPublished')->default(0);  
            $table->uuid('questionBankId');
            $table->uuid('creatorId');
            
            $table->timestamps();
            
            $table->foreign('questionBankId')->references('id')->on('questionBank')->onDelete('cascade');
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

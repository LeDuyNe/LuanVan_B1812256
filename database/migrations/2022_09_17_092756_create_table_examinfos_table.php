<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableExaminfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('examinfos', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('userID')->unsigned();
            $table->string('course');
            $table->integer('total_questions');
            $table->string('uniqueid');
            $table->string('time');

            $table->timestamps();
            
            $table->foreign('userID')->references('id')->on('users')->onDelete('cascade');
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

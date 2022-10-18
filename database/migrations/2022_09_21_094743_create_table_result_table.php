<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableResultTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('result', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('numCorrect')->nullable();
            $table->integer('restTime')->nullable();
            $table->string('note')->nullable();
            $table->uuid('examineeId');
            $table->uuid('questionBankId');

            $table->timestamps();

            $table->foreign('examineeId')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('questionBankId')->references('id')->on('questionbank')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('result');
    }
}

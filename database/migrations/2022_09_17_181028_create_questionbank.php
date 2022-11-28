<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuestionbank extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('questionbank', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->unique();
            $table->string('note')->nullable();
            $table->boolean('isPublished')->default(0);  
            $table->uuid('categoryId');
            $table->uuid('creatorId');

            $table->timestamps();
            
            $table->foreign('categoryId')->references('id')->on('categories')->onDelete('cascade');
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
        Schema::dropIfExists('question_bank');
    }
}

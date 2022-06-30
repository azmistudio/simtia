<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('academic.exam_score_final_weights', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('info_id')->nullable();
            $table->foreignId('exam_id')
                ->constrained('academic.exams')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->foreignId('lesson_exam_id')
                ->constrained('academic.lesson_exams')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->double('score', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('academic.exam_score_final_weights');
    }
};

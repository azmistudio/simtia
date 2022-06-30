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
        Schema::create('academic.exam_score_final_weight_infos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')
                ->constrained('academic.exams')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->foreignId('lesson_assessment_id')
                ->constrained('academic.lesson_assessments')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->string('remark')->nullable();
            $table->string('logged');
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
        Schema::dropIfExists('academic.exam_score_final_weight_infos');
    }
};

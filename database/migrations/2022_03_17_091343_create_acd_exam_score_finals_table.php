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
        Schema::create('academic.exam_score_finals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_id')
                ->constrained('academic.lessons')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->foreignId('student_id')
                ->constrained('academic.students')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->foreignId('class_id')
                ->constrained('academic.classes')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->foreignId('semester_id')
                ->constrained('academic.semesters')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->foreignId('lesson_exam_id')
                ->constrained('academic.lesson_exams')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->foreignId('lesson_assessment_id')
                ->constrained('academic.lesson_assessments')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->double('score', 10, 2);
            $table->string('remark')->nullable();
            $table->string('logged');
            $table->timestamps();
            $table->unique(['lesson_id','student_id','class_id','semester_id','lesson_exam_id','lesson_assessment_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('academic.exam_score_finals');
    }
};

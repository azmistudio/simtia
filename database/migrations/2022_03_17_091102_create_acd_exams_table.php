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
        Schema::create('academic.exams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')
                ->constrained('academic.teachers')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->foreignId('lesson_id')
                ->constrained('academic.lessons')
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
            $table->foreignId('employee_id')
                ->constrained('public.employees')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->foreignId('status_id')
                ->constrained('public.references')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->foreignId('score_aspect_id')
                ->constrained('academic.score_aspects')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->foreignId('lesson_exam_id')
                ->constrained('academic.lesson_exams')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->string('description')->nullable();
            $table->date('date');
            $table->foreignId('lesson_assessment_id')
                ->constrained('academic.lesson_assessments')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->bigInteger('lesson_plan_id');
            $table->string('code');
            $table->string('logged');
            $table->timestamps();
            $table->unique([
                'teacher_id',
                'lesson_id',
                'class_id',
                'semester_id',
                'employee_id',
                'status_id',
                'score_aspect_id',
                'lesson_exam_id',
                'date'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('academic.exams');
    }
};

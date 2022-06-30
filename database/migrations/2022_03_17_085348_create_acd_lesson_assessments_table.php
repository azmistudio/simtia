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
        Schema::create('academic.lesson_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')
                ->constrained('public.employees')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->foreignId('grade_id')
                ->constrained('academic.grades')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->foreignId('lesson_id')
                ->constrained('academic.lessons')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->foreignId('score_aspect_id')
                ->constrained('academic.score_aspects')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->foreignId('exam_id')
                ->constrained('academic.lesson_exams')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->double('value', 3, 1)->nullable();
            $table->string('logged');
            $table->timestamps();
            $table->unique(['employee_id','grade_id','lesson_id','score_aspect_id','exam_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('academic.lesson_assessments');
    }
};

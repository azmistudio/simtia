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
        Schema::create('academic.presence_lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')
                ->constrained('academic.classes')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->foreignId('semester_id')
                ->constrained('academic.semesters')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->foreignId('lesson_id')
                ->constrained('academic.lessons')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->date('date');
            $table->time('time')->nullable();
            $table->foreignId('employee_id')
                ->constrained('public.employees')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->string('remark')->nullable();
            $table->string('subject')->nullable();
            $table->string('objective')->nullable();
            $table->string('reflection')->nullable();
            $table->string('plan')->nullable();
            $table->tinyInteger('late')->nullable();
            $table->time('times');
            $table->foreignId('teacher_type')
                ->constrained('public.references')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->foreignId('lesson_schedule_id')
                ->constrained('academic.lesson_schedules')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->string('logged');
            $table->timestamps();
            $table->unique(['class_id', 'semester_id', 'lesson_id', 'date', 'employee_id', 'lesson_schedule_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('academic.presence_lessons');
    }
};

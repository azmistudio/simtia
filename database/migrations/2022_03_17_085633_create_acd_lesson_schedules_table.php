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
        Schema::create('academic.lesson_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')
                ->constrained('academic.classes')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->foreignId('employee_id')
                ->constrained('public.employees')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->foreignId('department_id')
                ->constrained('public.departments')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->foreignId('schedule_id')
                ->constrained('academic.lesson_schedule_infos')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->foreignId('teaching_id')
                ->constrained('academic.lesson_schedule_teachings')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->foreignId('lesson_id')
                ->constrained('academic.lessons')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->tinyInteger('day');
            $table->tinyInteger('from_time');
            $table->tinyInteger('to_time');
            $table->tinyInteger('feature');
            $table->foreignId('teaching_status')
                ->constrained('public.references')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->string('remark')->nullable();
            $table->time('start');
            $table->time('end');
            $table->foreignId('time_id_1')
                ->constrained('academic.lesson_schedule_times')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->foreignId('time_id_2')
                ->constrained('academic.lesson_schedule_times')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->timestamps();
            $table->unique(['class_id','employee_id','department_id','schedule_id','lesson_id','day','from_time','teaching_status']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('academic.lesson_schedules');
    }
};

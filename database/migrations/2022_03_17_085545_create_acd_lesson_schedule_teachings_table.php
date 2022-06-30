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
        Schema::create('academic.lesson_schedule_teachings', function (Blueprint $table) {
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
            $table->string('logged');
            $table->timestamps();
            $table->unique(['class_id','employee_id','department_id','schedule_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('academic.lesson_schedule_teachings');
    }
};

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
        Schema::create('academic.student_alumnis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')
                ->constrained('academic.students')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->foreignId('end_class')
                ->constrained('academic.classes')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->foreignId('end_grade')
                ->constrained('academic.grades')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->date('graduate_date');
            $table->string('remark')->nullable();
            $table->foreignId('department_id')
                ->constrained('public.departments')
                ->onUpdate('cascade')
                ->onDelete('restrict');
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
        Schema::dropIfExists('academic.student_alumnis');
    }
};

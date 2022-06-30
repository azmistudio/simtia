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
        Schema::create('academic.classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grade_id')
                ->constrained('academic.grades')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->foreignId('schoolyear_id')
                ->constrained('academic.schoolyears')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->string('class');
            $table->foreignId('employee_id')
                ->constrained('public.employees')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->tinyInteger('capacity');
            $table->string('remark')->nullable();
            $table->tinyInteger('is_active');
            $table->string('logged');
            $table->timestamps();
            $table->unique(['grade_id','schoolyear_id','class']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('academic.classes');
    }
};

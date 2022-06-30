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
        Schema::create('academic.student_dept_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')
                ->constrained('academic.students')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->foreignId('department_id')
                ->constrained('public.departments')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->date('start_date');
            $table->tinyInteger('active');
            $table->tinyInteger('status');
            $table->bigInteger('old_student_id')->nullable();
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
        Schema::dropIfExists('academic.student_dept_histories');
    }
};

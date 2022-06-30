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
        Schema::create('academic.presence_lesson_students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('presence_id')
                ->constrained('academic.presence_lessons')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->foreignId('student_id')
                ->constrained('academic.students')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->tinyInteger('presence');
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
        Schema::dropIfExists('academic.presence_lesson_students');
    }
};

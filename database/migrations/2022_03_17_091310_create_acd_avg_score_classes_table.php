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
        Schema::create('academic.avg_score_classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('semester_id')
                ->constrained('academic.semesters')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->foreignId('class_id')
                ->constrained('academic.classes')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->foreignId('exam_id')
                ->constrained('academic.exams')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->double('avg_score', 10, 2);
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
        Schema::dropIfExists('academic.avg_score_classes');
    }
};

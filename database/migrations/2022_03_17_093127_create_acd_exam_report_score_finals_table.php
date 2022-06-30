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
        Schema::create('academic.exam_report_score_finals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_report_id')
                ->constrained('academic.exam_reports')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->foreignId('student_id')
                ->constrained('academic.students')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->foreignId('lesson_assessment_id')
                ->constrained('academic.lesson_assessments')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->foreignId('exam_report_info_id')
                ->constrained('academic.exam_report_score_infos')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->double('value', 10, 2);
            $table->string('value_letter');
            $table->text('comment')->nullable();
            $table->timestamps();
            $table->unique(['exam_report_id','student_id','exam_report_info_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('academic.exam_report_score_finals');
    }
};

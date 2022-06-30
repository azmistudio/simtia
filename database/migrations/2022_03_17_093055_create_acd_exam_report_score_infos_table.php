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
        Schema::create('academic.exam_report_score_infos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_report_id')
                ->constrained('academic.exam_reports')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->foreignId('lesson_id')
                ->constrained('academic.lessons')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->foreignId('class_id')
                ->constrained('academic.classes')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->foreignId('semester_id')
                ->constrained('academic.semesters')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->double('value', 10, 2);
            $table->timestamps();
            $table->unique(['exam_report_id','lesson_id','class_id','semester_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('academic.exam_report_score_infos');
    }
};

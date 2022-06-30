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
        Schema::create('academic.admission_configs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admission_id')
                ->constrained('academic.admissions')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->string('donate_code_1')->nullable();
            $table->string('donate_name_1')->nullable();
            $table->string('donate_code_2')->nullable();
            $table->string('donate_name_2')->nullable();
            $table->string('exam_code_01')->nullable();
            $table->string('exam_name_01')->nullable();
            $table->string('exam_code_02')->nullable();
            $table->string('exam_name_02')->nullable();
            $table->string('exam_code_03')->nullable();
            $table->string('exam_name_03')->nullable();
            $table->string('exam_code_04')->nullable();
            $table->string('exam_name_04')->nullable();
            $table->string('exam_code_05')->nullable();
            $table->string('exam_name_05')->nullable();
            $table->string('exam_code_06')->nullable();
            $table->string('exam_name_06')->nullable();
            $table->string('exam_code_07')->nullable();
            $table->string('exam_name_07')->nullable();
            $table->string('exam_code_08')->nullable();
            $table->string('exam_name_08')->nullable();
            $table->string('exam_code_09')->nullable();
            $table->string('exam_name_09')->nullable();
            $table->string('exam_code_10')->nullable();
            $table->string('exam_name_10')->nullable();
            $table->string('logged');
            $table->timestamps();
            $table->unique('admission_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('academic.admission_configs');
    }
};

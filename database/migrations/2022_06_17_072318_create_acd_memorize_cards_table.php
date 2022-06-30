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
        Schema::create('academic.memorize_cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')
                ->constrained('academic.classes')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->foreignId('student_id')
                ->constrained('academic.students')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->foreignId('employee_id')
                ->constrained('public.employees')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->date('memorize_date');
            $table->foreignId('from_surah_id')
                ->constrained('public.quran_surahs')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->foreignId('to_surah_id')
                ->constrained('public.quran_surahs')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->tinyInteger('from_verse');
            $table->tinyInteger('to_verse');
            $table->string('status');
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
        Schema::dropIfExists('academic.memorize_cards');
    }
};

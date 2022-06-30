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
        Schema::create('academic.lesson_schedule_infos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('schoolyear_id')
                ->constrained('academic.schoolyears')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->string('description');
            $table->tinyInteger('is_active');
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
        Schema::dropIfExists('academic.lesson_schedule_infos');
    }
};

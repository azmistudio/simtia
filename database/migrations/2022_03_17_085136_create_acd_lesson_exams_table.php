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
        Schema::create('academic.lesson_exams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_id')
                ->constrained('academic.lessons')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->foreignId('score_aspect_id')
                ->constrained('academic.score_aspects')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->string('code');
            $table->string('subject');
            $table->string('remark')->nullable();
            $table->string('logged');
            $table->timestamps();
            $table->unique(['lesson_id','score_aspect_id','code']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('academic.lesson_exams');
    }
};

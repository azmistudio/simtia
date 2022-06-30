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
        Schema::create('academic.exam_report_comment_socials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_id')
                ->constrained('academic.lessons')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->foreignId('type_id')
                ->constrained('public.references')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->foreignId('grade_id')
                ->constrained('academic.grades')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->string('aspect');
            $table->text('comment')->nullable();
            $table->tinyInteger('is_active')->nullable();
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
        Schema::dropIfExists('academic.exam_report_comment_socials');
    }
};

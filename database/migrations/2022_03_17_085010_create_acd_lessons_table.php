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
        Schema::create('academic.lessons', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->string('name');
            $table->foreignId('department_id')
                ->constrained('public.departments')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->foreignId('group_id')
                ->constrained('academic.lesson_groups')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->tinyInteger('mandatory');
            $table->string('remark')->nullable();
            $table->tinyInteger('is_active');
            $table->string('logged');
            $table->timestamps();
            $table->unique(['code','department_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('academic.lessons');
    }
};

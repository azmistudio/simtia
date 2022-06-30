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
        Schema::create('academic.lesson_groups', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->string('group');
            $table->tinyInteger('order');
            $table->string('logged');
            $table->timestamps();
            $table->unique(['code','order']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('academic.lesson_groups');
    }
};

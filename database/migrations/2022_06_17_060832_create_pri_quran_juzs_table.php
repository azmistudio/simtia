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
        Schema::create('public.quran_juzs', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('total');
            $table->bigInteger('from_surah');
            $table->tinyInteger('from_ayah');
            $table->bigInteger('to_surah');
            $table->tinyInteger('to_ayah');
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
        Schema::dropIfExists('public.quran_juzs');
    }
};

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
        Schema::create('references', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->string('name');
            $table->string('category');
            $table->string('remark');
            $table->tinyInteger('order');
            $table->tinyInteger('parent');
            $table->timestamps();
            $table->unique(['name','category']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('references');
    }
};

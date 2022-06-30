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
        Schema::create('finance.receipt_categories', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('category');
            $table->tinyInteger('order');
            $table->tinyInteger('student');
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
        Schema::dropIfExists('finance.receipt_categories');
    }
};

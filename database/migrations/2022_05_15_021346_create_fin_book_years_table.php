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
        Schema::create('finance.book_years', function (Blueprint $table) {
            $table->id();
            $table->string('book_year');
            $table->date('start_date');
            $table->date('end_date');
            $table->string('prefix');
            $table->string('remark')->nullable();
            $table->bigInteger('number');
            $table->tinyInteger('is_active');
            $table->string('logged');
            $table->timestamps();
            $table->unique('book_year');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('finance.book_years');
    }
};

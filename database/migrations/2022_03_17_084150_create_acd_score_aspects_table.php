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
        Schema::create('academic.score_aspects', function (Blueprint $table) {
            $table->id();
            $table->string('basis');
            $table->string('remark')->nullable();
            $table->string('logged');
            $table->timestamps();
            $table->unique('basis');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('academic.score_aspects');
    }
};

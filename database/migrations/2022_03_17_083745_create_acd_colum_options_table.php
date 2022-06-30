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
        Schema::create('academic.column_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('column_id')
                ->constrained('academic.columns')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->string('name');
            $table->tinyInteger('order');
            $table->tinyInteger('is_active');
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
        Schema::dropIfExists('academic.column_options');
    }
};

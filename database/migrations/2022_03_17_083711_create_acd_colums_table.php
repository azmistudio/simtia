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
        Schema::create('academic.columns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')
                ->constrained('public.departments')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->string('name');
            $table->string('remark')->nullable();
            $table->tinyInteger('type');
            $table->tinyInteger('order');
            $table->tinyInteger('is_active');
            $table->timestamps();
            $table->unique(['department_id','name']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('academic.colums');
    }
};

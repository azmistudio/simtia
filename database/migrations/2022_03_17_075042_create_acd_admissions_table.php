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
        Schema::create('academic.admissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')
                ->constrained('public.departments')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->string('name');
            $table->string('prefix')->nullable();
            $table->tinyInteger('is_active');
            $table->string('remark')->nullable();
            $table->string('logged');
            $table->timestamps();
            $table->unique(['department_id','prefix']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('academic.admissions');
    }
};

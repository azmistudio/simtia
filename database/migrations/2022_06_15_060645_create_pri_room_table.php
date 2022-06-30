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
        Schema::create('public.rooms', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->tinyInteger('is_employee')->default(0);
            $table->tinyInteger('gender');
            $table->tinyInteger('capacity');
            $table->foreignId('employee_id')
                ->nullable()
                ->constrained('public.employees')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->foreignId('department_id')
                ->constrained('public.departments')
                ->onUpdate('cascade')
                ->onDelete('restrict');
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
        Schema::dropIfExists('public.rooms');
    }
};

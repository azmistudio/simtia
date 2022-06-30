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
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->foreignId('employee_id')
                ->nullable()
                ->constrained('public.employees')
                ->onUpdate('cascade')
                ->onDelete('no action')
                ->remark('headmaster');
            $table->string('remark')->nullable();
            $table->tinyInteger('is_active');
            $table->tinyInteger('is_all');
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
        Schema::dropIfExists('departments');
    }
};

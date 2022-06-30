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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->integer('employee_id')->unique();
            $table->string('name');
            $table->string('title')->nullable();
            $table->string('title_first')->nullable();
            $table->string('title_end')->nullable();
            $table->tinyInteger('gender');
            $table->string('pob');
            $table->date('dob');
            $table->integer('religion');
            $table->foreignId('section')
                ->constrained('public.references')
                ->onUpdate('cascade')
                ->onDelete('no action');
            $table->foreignId('tribe')
                ->constrained('public.references')
                ->onUpdate('cascade')
                ->onDelete('no action');    
            $table->tinyInteger('marital');
            $table->string('national_id')->nullable();
            $table->string('address');
            $table->string('phone')->nullable();
            $table->string('mobile');
            $table->string('email')->unique();
            $table->string('photo')->nullable();
            $table->string('remark')->nullable();
            $table->string('status')->nullable();
            $table->date('work_start');
            $table->tinyInteger('is_active');
            $table->string('remark_nonactive')->nullable();
            $table->tinyInteger('is_retired');
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
        Schema::dropIfExists('employees');
    }
};

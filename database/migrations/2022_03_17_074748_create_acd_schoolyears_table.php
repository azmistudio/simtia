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
        Schema::create('academic.schoolyears', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')
                ->constrained('public.departments')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->string('school_year');
            $table->date('start_date');
            $table->date('end_date');
            $table->string('remark')->nullable();
            $table->tinyInteger('is_active');
            $table->string('logged');
            $table->timestamps();
            $table->unique(['department_id','school_year']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('academic.schoolyears');
    }
};

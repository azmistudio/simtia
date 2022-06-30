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
        Schema::create('academic.prospect_student_groups', function (Blueprint $table) {
            $table->id();
            $table->string('group');
            $table->foreignId('admission_id')
                ->constrained('academic.admissions')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->integer('capacity');
            $table->string('remark')->nullable();
            $table->string('logged');
            $table->timestamps();
            $table->unique(['group','admission_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('academic.prospect_student_groups');
    }
};

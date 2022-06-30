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
        Schema::create('academic.column_prospect_students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prospect_student_id')
                ->constrained('academic.prospect_students')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->foreignId('column_id')
                ->constrained('academic.columns')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->tinyInteger('type');
            $table->string('values');
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
        Schema::dropIfExists('academic.column_prospect_students');
    }
};

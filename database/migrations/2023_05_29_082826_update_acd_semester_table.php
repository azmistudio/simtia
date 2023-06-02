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
        //
        Schema::table('academic.semesters', function (Blueprint $table) {
            $table->foreignId('grade_id')
                ->nullable()
                ->constrained('academic.grades')
                ->onUpdate('cascade')
                ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('academic.semesters', function (Blueprint $table) {
            $table->dropColumn('grade_id');
        });
    }
};

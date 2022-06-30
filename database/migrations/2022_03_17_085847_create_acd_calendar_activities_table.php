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
        Schema::create('academic.calendar_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('calendar_id')
                ->constrained('academic.calendars')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->date('start');
            $table->date('end');
            $table->string('activity');
            $table->text('description');
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
        Schema::dropIfExists('academic.calendar_activities');
    }
};

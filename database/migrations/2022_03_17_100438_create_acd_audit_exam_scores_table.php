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
        Schema::create('academic.audit_exam_scores', function (Blueprint $table) {
            $table->id();
            $table->string('score_type');
            $table->bigInteger('exam_id');
            $table->double('score_before', 10, 2);
            $table->double('score_after', 10, 2);
            $table->dateTime('date_trans');
            $table->string('reason')->nullable();
            $table->string('remark')->nullable();
            $table->string('info')->nullable();
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
        Schema::dropIfExists('academic.audit_exam_scores');
    }
};

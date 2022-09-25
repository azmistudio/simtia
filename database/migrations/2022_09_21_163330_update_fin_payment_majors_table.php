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
        Schema::table('finance.payment_majors', function (Blueprint $table) {
            $table->char('period_month',2)->nullable();
            $table->tinyInteger('period_year')->nullable();
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
        Schema::table('finance.payment_majors', function (Blueprint $table) {
            $table->dropColumn('period_month');
            $table->dropColumn('period_year');
        });
    }
};

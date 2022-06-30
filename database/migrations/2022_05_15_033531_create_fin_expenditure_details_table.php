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
        Schema::create('finance.expenditure_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expenditure_id')
                ->constrained('finance.expenditures')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->foreignId('account_id')
                ->constrained('finance.codes')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->string('remark')->nullable();
            $table->decimal('amount', $precision = 15, $scale = 2)->default(0);
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
        Schema::dropIfExists('finance.expenditure_details');
    }
};

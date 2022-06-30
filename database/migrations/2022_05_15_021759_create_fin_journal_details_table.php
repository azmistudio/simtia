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
        Schema::create('finance.journal_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('journal_id')
                ->constrained('finance.journals')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->foreignId('account_id')
                ->constrained('finance.codes')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->decimal('debit', $precision = 15, $scale = 2)->default(0);
            $table->decimal('credit', $precision = 15, $scale = 2)->default(0);
            $table->integer('uuid');
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
        Schema::dropIfExists('finance.journal_details');
    }
};

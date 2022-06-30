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
        Schema::create('finance.journals', function (Blueprint $table) {
            $table->id();
            $table->date('journal_date');
            $table->string('transaction');
            $table->string('cash_no');
            $table->foreignId('employee_id')
                ->nullable()
                ->constrained('public.employees')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->foreignId('bookyear_id')
                ->constrained('finance.book_years')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->string('source');
            $table->string('remark')->nullable();
            $table->foreignId('department_id')
                ->constrained('public.departments')
                ->onUpdate('cascade')
                ->onDelete('restrict');
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
        Schema::dropIfExists('finance.journals');
    }
};

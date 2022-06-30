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
        Schema::create('finance.expenditure_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')
                ->constrained('public.departments')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->string('name');
            $table->foreignId('debit_account')
                ->constrained('finance.codes')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->foreignId('credit_account')
                ->constrained('finance.codes')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->decimal('amount', $precision = 15, $scale = 2)->default(0);
            $table->string('remark')->nullable();
            $table->tinyInteger('is_active');
            $table->string('logged');
            $table->timestamps();
            $table->unique(['department_id','name']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('finance.expenditure_types');
    }
};

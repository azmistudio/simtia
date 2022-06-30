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
        Schema::create('finance.saving_types', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('is_employee')->default(0);
            $table->string('name');
            $table->foreignId('cash_account')
                ->constrained('finance.codes')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->foreignId('credit_account')
                ->constrained('finance.codes')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->foreignId('department_id')
                ->constrained('public.departments')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->string('remark')->nullable();
            $table->tinyInteger('is_active')->default(1);
            $table->string('transaction_type')->nullable();
            $table->string('logged');
            $table->timestamps();
            $table->unique(['name','cash_account','credit_account','department_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('finance.saving_types');
    }
};

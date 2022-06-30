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
        Schema::create('finance.receipt_majors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('major_id')
                ->constrained('finance.payment_majors')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->foreignId('journal_id')
                ->constrained('finance.journals')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->date('trans_date');
            $table->decimal('total', $precision = 15, $scale = 2)->default(0);
            $table->foreignId('employee_id')
                ->nullable()
                ->constrained('public.employees')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->string('remark')->nullable();
            $table->string('reason')->nullable();
            $table->tinyInteger('first_instalment')->default(0);
            $table->tinyInteger('is_prospect')->default(0);
            $table->decimal('discount_amount', $precision = 15, $scale = 2)->default(0);
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
        Schema::dropIfExists('finance.receipt_majors');
    }
};

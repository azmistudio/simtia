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
        Schema::create('finance.savings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')
                ->nullable()
                ->constrained('public.employees')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->foreignId('student_id')
                ->nullable()
                ->constrained('academic.students')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->tinyInteger('is_employee')->default(0);
            $table->foreignId('saving_id')
                ->constrained('finance.saving_types')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->foreignId('journal_id')
                ->constrained('finance.journals')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->date('trans_date');
            $table->decimal('debit', $precision = 15, $scale = 2)->default(0);
            $table->decimal('credit', $precision = 15, $scale = 2)->default(0);
            $table->string('employee')->nullable();
            $table->string('remark')->nullable();
            $table->string('reason')->nullable();
            $table->foreignId('bookyear_id')
                ->constrained('finance.book_years')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->string('transaction_type')->nullable();
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
        Schema::dropIfExists('finance.savings');
    }
};

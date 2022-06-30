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
        Schema::create('finance.payment_majors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')
                ->constrained('public.departments')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->foreignId('category_id')
                ->constrained('finance.receipt_categories')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->foreignId('prospect_student_id')
                ->nullable()
                ->constrained('academic.prospect_students')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->foreignId('student_id')
                ->nullable()
                ->constrained('academic.students')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->foreignId('receipt_id')
                ->constrained('finance.receipt_types')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->decimal('amount', $precision = 15, $scale = 2)->default(0);
            $table->decimal('instalment', $precision = 15, $scale = 2)->default(0);
            $table->tinyInteger('is_paid')->default(0);
            $table->tinyInteger('is_prospect')->default(0);
            $table->foreignId('journal_id')
                ->constrained('finance.journals')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->foreignId('bookyear_id')
                ->constrained('finance.book_years')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->string('remark')->nullable();
            $table->string('reason')->nullable();
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
        Schema::dropIfExists('finance.payment_majors');
    }
};

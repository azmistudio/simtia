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
        Schema::create('finance.receipt_voluntaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('receipt_id')
                ->constrained('finance.receipt_types')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->foreignId('journal_id')
                ->constrained('finance.journals')
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
            $table->date('trans_date');
            $table->decimal('total', $precision = 15, $scale = 2)->default(0);
            $table->string('employee')->nullable();
            $table->tinyInteger('is_prospect')->default(0);
            $table->foreignId('bookyear_id')
                ->constrained('finance.book_years')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->foreignId('department_id')
                ->constrained('public.departments')
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
        Schema::dropIfExists('finance.receipt_voluntaries');
    }
};

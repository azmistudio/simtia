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
        Schema::create('finance.audit_receipt_voluntaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('audit_id')
                ->constrained('finance.audits')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->tinyInteger('is_status')->default(0);
            $table->tinyInteger('is_prospect')->default(0);
            $table->foreignId('voluntary_id')
                ->constrained('finance.receipt_voluntaries')
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
            $table->string('remark')->nullable();
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
        Schema::dropIfExists('finance.audit_receipt_voluntaries');
    }
};

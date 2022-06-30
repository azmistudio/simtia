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
        Schema::create('finance.audit_receipt_majors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('audit_id')
                ->constrained('finance.audits')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->foreignId('major_id')
                ->constrained('finance.payment_majors')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->foreignId('journal_id')
                ->constrained('finance.journals')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->tinyInteger('is_prospect')->default(0);
            $table->tinyInteger('is_status')->default(0);
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
        Schema::dropIfExists('finance.audit_receipt_majors');
    }
};

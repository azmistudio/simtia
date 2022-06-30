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
        Schema::create('finance.audit_journal_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('audit_id')
                ->constrained('finance.audits')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->tinyInteger('is_status')->default(0);
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
        Schema::dropIfExists('finance.audit_journal_details');
    }
};

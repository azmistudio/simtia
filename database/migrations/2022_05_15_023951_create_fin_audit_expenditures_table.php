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
        Schema::create('finance.audit_expenditures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('audit_id')
                ->constrained('finance.audits')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->tinyInteger('is_status')->default(0);
            $table->foreignId('expenditure_id')
                ->constrained('finance.expenditures')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->string('purpose')->nullable();
            $table->tinyInteger('requested_by')->default(1);
            $table->bigInteger('employee_id')->nullable();
            $table->bigInteger('student_id')->nullable();
            $table->bigInteger('requested_id')->nullable();
            $table->string('received_name')->nullable();
            $table->string('requested_name')->nullable();
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
        Schema::dropIfExists('finance.audit_expenditures');
    }
};

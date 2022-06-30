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
        Schema::create('finance.begin_balances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bookyear_id')
                ->constrained('finance.book_years')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->date('trans_date');
            $table->foreignId('account_id')
                ->constrained('finance.codes')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->decimal('total', $precision = 15, $scale = 2)->default(0);
            $table->char('pos',1);
            $table->string('reason')->nullable();
            $table->string('logged');
            $table->timestamps();
            $table->unique(['bookyear_id','trans_date','account_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('finance.begin_balances');
    }
};

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
        Schema::create('finance.codes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')
                ->constrained('finance.code_categories')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->string('code')->unique();
            $table->string('name');
            $table->smallInteger('locked')->default(0);
            $table->string('remark')->nullable();
            $table->decimal('balance', $precision = 15, $scale = 2);
            $table->bigInteger('parent');
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
        Schema::dropIfExists('finance.codes');
    }
};

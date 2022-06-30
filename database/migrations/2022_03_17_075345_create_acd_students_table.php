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
        Schema::create('academic.students', function (Blueprint $table) {
            $table->id();
            $table->string('student_no')->unique();
            $table->string('name');
            $table->string('surname')->nullable();
            $table->tinyInteger('year_entry');
            $table->foreignId('prospect_student_group_id')
                ->constrained('academic.prospect_student_groups')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->foreignId('class_id')
                ->constrained('academic.classes')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->foreignId('tribe')
                ->constrained('public.references')
                ->onUpdate('cascade')
                ->onDelete('restrict');    
            $table->integer('religion');
            $table->foreignId('student_status')
                ->constrained('public.references')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->foreignId('economic')
                ->constrained('public.references')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->tinyInteger('gender');
            $table->string('pob');
            $table->date('dob');
            $table->foreignId('citizen')
                ->constrained('public.references')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->tinyInteger('child_no');
            $table->tinyInteger('child_brother');
            $table->foreignId('child_status')
                ->constrained('public.references')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->tinyInteger('child_brother_sum')->nullable();
            $table->tinyInteger('child_step_sum')->nullable();
            $table->string('language')->nullable();
            $table->double('weight', 3, 2)->nullable();
            $table->double('height', 3, 2)->nullable();
            $table->foreignId('blood')
                ->constrained('public.references')
                ->onUpdate('cascade')
                ->onDelete('no action')
                ->nullable();
            $table->string('photo')->nullable();
            $table->text('address')->nullable();
            $table->tinyInteger('distance')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('phone')->nullable();
            $table->string('mobile')->nullable();
            $table->string('email')->nullable();
            $table->text('medical')->nullable();
            $table->string('father')->nullable();
            $table->string('mother')->nullable();
            $table->tinyInteger('father_status')->nullable();
            $table->tinyInteger('mother_status')->nullable();
            $table->tinyInteger('is_father_died')->nullable();
            $table->tinyInteger('is_mother_died')->nullable();
            $table->string('father_pob');
            $table->string('mother_pob');
            $table->date('father_dob');
            $table->date('mother_dob');
            $table->foreignId('father_education')
                ->constrained('public.references')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->foreignId('mother_education')
                ->constrained('public.references')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->foreignId('father_job')
                ->constrained('public.references')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->foreignId('mother_job')
                ->constrained('public.references')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->double('father_income', 8, 2)->nullable();
            $table->double('mother_income', 8, 2)->nullable();
            $table->string('father_email')->nullable();
            $table->string('mother_email')->nullable();
            $table->string('parent_guardian')->nullable();
            $table->text('parent_address')->nullable();
            $table->string('father_mobile')->nullable();
            $table->string('mother_mobile')->nullable();
            $table->text('hobby')->nullable();
            $table->text('mail_address')->nullable();
            $table->string('remark')->nullable();
            $table->foreignId('prospect_student_id')
                ->constrained('academic.prospect_students')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->string('remark_admission')->nullable();
            $table->foreignId('mutation')
                ->nullable()
                ->constrained('public.references')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->tinyInteger('alumni')->default(0);
            $table->tinyInteger('is_active');
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
        Schema::dropIfExists('academic.students');
    }
};

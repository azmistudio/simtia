<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $view = "CREATE OR REPLACE VIEW academic.prospect_students_view AS (
                    SELECT a.*, b.group, c.department_id FROM academic.prospect_students a
                    JOIN academic.prospect_student_groups b ON b.id = a.prospect_group_id
                    JOIN academic.admissions c ON c.id = b.admission_id
                    WHERE a.id NOT IN (SELECT prospect_student_id FROM academic.students) AND a.is_active = 1
                )";
        DB::unprepared($view);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared("DROP VIEW academic.prospect_students_view");
    }
};

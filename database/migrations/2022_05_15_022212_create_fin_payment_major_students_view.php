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
        $view = "CREATE OR REPLACE VIEW finance.payment_major_students_view AS (
                    SELECT a.student_id, b.student_no, INITCAP(b.name) AS name, a.is_prospect, b.class_id, 0::bigint as group_id, a.department_id
                    FROM finance.payment_majors a
                    JOIN academic.students b ON b.id = a.student_id
                    WHERE a.student_id IS NOT NULL
                    GROUP BY a.student_id, b.student_no, b.name, a.is_prospect, b.class_id, a.department_id
                    UNION ALL
                    SELECT a.prospect_student_id AS student_id, b.registration_no, initcap((b.name)::text) AS name, a.is_prospect, 0::bigint as class_id, c.id AS group_id, a.department_id
                    FROM finance.payment_majors a
                    JOIN academic.prospect_students b ON b.id = a.prospect_student_id
                    JOIN academic.prospect_student_groups c ON c.id = b.prospect_group_id
                    WHERE a.is_prospect = 1
                    GROUP BY a.prospect_student_id, b.registration_no, b.name, a.is_prospect, c.id, a.department_id
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
        DB::unprepared("DROP VIEW finance.payment_major_students_view");
    }
};

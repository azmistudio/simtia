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
        $view = "CREATE OR REPLACE VIEW academic.prospect_groups_view AS (
                    SELECT academic.prospect_student_groups.id, admission_id, \"group\", 
                        capacity, COUNT(prospect_group_id) AS occupied, 
                        CONCAT(capacity,'/',COUNT(prospect_group_id)) AS quota,
                        UPPER(public.departments.name) AS department, academic.admissions.department_id, academic.admissions.is_active
                    FROM academic.prospect_student_groups
                    INNER JOIN academic.admissions ON academic.admissions.id = academic.prospect_student_groups.admission_id
                    INNER JOIN public.departments ON public.departments.id = academic.admissions.department_id
                    LEFT JOIN (SELECT prospect_group_id FROM academic.prospect_students WHERE is_active = 1) AS prospective 
                    ON academic.prospect_student_groups.id = prospective.prospect_group_id
                    GROUP BY academic.prospect_student_groups.id, \"group\", admission_id, capacity, department, academic.admissions.department_id, academic.admissions.is_active
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
        DB::unprepared("DROP VIEW academic.prospect_groups_view");
    }
};

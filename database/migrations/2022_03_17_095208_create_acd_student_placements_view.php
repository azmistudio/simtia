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
        $view = "CREATE OR REPLACE VIEW academic.student_placements_view AS (
                    SELECT CONCAT(academic.students.prospect_student_group_id,academic.students.class_id,public.departments.id,academic.grades.id) AS seq,
                    academic.students.prospect_student_group_id, academic.students.class_id, public.departments.id AS department_id, UPPER(academic.classes.class) AS class, UPPER(public.departments.name) AS department, UPPER(academic.admissions.name) AS admission,
                    academic.schoolyears.school_year, academic.grades.grade, UPPER(academic.prospect_student_groups.group) AS groupname
                    FROM academic.students
                    INNER JOIN academic.classes ON academic.classes.id = academic.students.class_id
                    INNER JOIN academic.prospect_student_groups ON academic.prospect_student_groups.id = academic.students.prospect_student_group_id
                    INNER JOIN academic.schoolyears ON academic.schoolyears.id = academic.classes.schoolyear_id
                    INNER JOIN public.departments ON public.departments.id = academic.schoolyears.department_id
                    INNER JOIN academic.grades ON academic.grades.id = academic.classes.grade_id
                    INNER JOIN academic.admissions ON academic.admissions.id = academic.prospect_student_groups.admission_id
                    WHERE academic.students.is_active = 1 AND academic.students.alumni = 0
                    GROUP BY academic.students.prospect_student_group_id, academic.students.class_id, academic.schoolyears.school_year, academic.grades.grade, public.departments.id, public.departments.name, academic.admissions.name, academic.classes.class, academic.grades.id, academic.prospect_student_groups.group
                    ORDER BY public.departments.name ASC
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
        DB::unprepared("DROP VIEW academic.student_placements_view");
    }
};

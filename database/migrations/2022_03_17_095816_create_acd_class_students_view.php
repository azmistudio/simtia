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
        $view = "CREATE OR REPLACE VIEW academic.class_students_view AS (
                    SELECT academic.classes.id, schoolyear_id, grade_id, UPPER(class) as class, capacity || '/' || COUNT(students.class_id) as capacity,
                        public.departments.id as department_id, UPPER(public.departments.name) as department, academic.schoolyears.school_year, academic.grades.grade, semesters.id AS semester_id, UPPER(semesters.semester) AS semester,
                        (SELECT ARRAY(select CONCAT(academic.schoolyears.start_date,'|',academic.schoolyears.end_date) FROM academic.schoolyears WHERE academic.schoolyears.id = academic.classes.schoolyear_id )) AS period, academic.schoolyears.is_active, COUNT(students.class_id) as occupied
                        FROM academic.classes
                        LEFT JOIN ( SELECT class_id FROM academic.students WHERE is_active = 1) AS students ON academic.classes.id = students.class_id
                        INNER JOIN academic.schoolyears ON academic.schoolyears.id = academic.classes.schoolyear_id
                        INNER JOIN academic.grades ON academic.grades.id = academic.classes.grade_id
                        INNER JOIN public.departments ON public.departments.id = academic.grades.department_id
                        LEFT JOIN ( SELECT id, department_id, semester FROM academic.semesters WHERE is_active = 1) AS semesters ON public.departments.id = semesters.department_id
                        WHERE academic.classes.is_active = 1 AND academic.schoolyears.is_active = 1
                        GROUP BY academic.classes.id, schoolyear_id, grade_id, class, public.departments.id, public.departments.name, academic.schoolyears.school_year, academic.grades.grade, semesters.id, semesters.semester, academic.schoolyears.is_active
                        ORDER BY academic.classes.id ASC
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
        DB::unprepared("DROP VIEW academic.class_students_view");
    }
};

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
        //
        DB::unprepared("DROP VIEW academic.lesson_schedules_view");
        $view = "CREATE OR REPLACE VIEW academic.lesson_schedules_view AS (
                    SELECT CONCAT(academic.lesson_schedules.class_id,'-',academic.lesson_schedules.employee_id,'-',academic.lesson_schedules.department_id,'-',academic.lesson_schedules.lesson_id) as seq,
                        academic.classes.id as id_class, academic.lesson_schedules.employee_id, academic.lesson_schedules.department_id, academic.lesson_schedules.lesson_id,
                        academic.lesson_schedules.teaching_status, UPPER(public.departments.name) as deptname, academic.grades.grade, academic.schoolyears.school_year,
                        academic.semesters.id as semester_id, UPPER(academic.semesters.semester) as semester, UPPER(academic.classes.class) as class, INITCAP(public.employees.name) as employee, UPPER(public.references.name) as status, INITCAP(academic.lessons.name) as lesson,
                        (SELECT ARRAY(select CONCAT(academic.schoolyears.start_date,'|',academic.schoolyears.end_date) FROM academic.schoolyears WHERE academic.schoolyears.id = academic.classes.schoolyear_id )) AS period, academic.classes.grade_id
                        FROM academic.lesson_schedules
                        INNER JOIN public.departments ON public.departments.id = academic.lesson_schedules.department_id
                        INNER JOIN academic.classes ON academic.classes.id = academic.lesson_schedules.class_id
                        INNER JOIN academic.grades ON academic.grades.id = academic.classes.grade_id
                        INNER JOIN academic.schoolyears ON academic.schoolyears.id = academic.classes.schoolyear_id
                        INNER JOIN public.employees ON public.employees.id = academic.lesson_schedules.employee_id
                        INNER JOIN academic.semesters ON academic.semesters.grade_id = academic.classes.grade_id
                        INNER JOIN academic.lessons ON academic.lessons.id = academic.lesson_schedules.lesson_id
                        INNER JOIN public.references ON public.references.id = academic.lesson_schedules.teaching_status
                        WHERE academic.semesters.is_active = 1
                        GROUP BY academic.lesson_schedules.class_id, academic.lesson_schedules.employee_id, academic.lesson_schedules.department_id, academic.lesson_schedules.lesson_id,
                        academic.lesson_schedules.teaching_status, public.departments.name, academic.classes.id, academic.classes.class, academic.classes.grade_id,
                        academic.grades.grade, academic.schoolyears.school_year, academic.semesters.semester,
                        academic.semesters.id, academic.lessons.name, public.employees.name, public.references.name ORDER BY department_id, lesson_id
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
        //
    }
};

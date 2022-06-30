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
        $view = "CREATE OR REPLACE VIEW academic.student_alumnis_view AS (
                    SELECT 
                        CONCAT(academic.student_alumnis.end_class,academic.student_alumnis.end_grade,academic.student_alumnis.department_id,DATE_PART('year', academic.student_alumnis.graduate_date)) AS seq,
                        academic.student_alumnis.end_class,
                        academic.student_alumnis.end_grade,
                        academic.student_alumnis.department_id,
                        DATE_PART('year', academic.student_alumnis.graduate_date) AS period, 
                        UPPER(academic.classes.class) AS class, 
                        UPPER(academic.grades.grade) AS grade, 
                        UPPER(public.departments.name) AS department
                    FROM academic.student_alumnis
                    INNER JOIN academic.classes ON academic.classes.id = academic.student_alumnis.end_class
                    INNER JOIN academic.grades ON academic.grades.id = academic.student_alumnis.end_grade
                    INNER JOIN public.departments ON public.departments.id = academic.student_alumnis.department_id
                    WHERE academic.student_alumnis.student_id IN (SELECT id FROM academic.students WHERE alumni = 1)
                    GROUP BY 
                        academic.student_alumnis.end_class, academic.student_alumnis.end_grade, academic.student_alumnis.department_id, academic.classes.class, 
                        academic.grades.grade, public.departments.name, DATE_PART('year', academic.student_alumnis.graduate_date)
                    ORDER BY DATE_PART('year', academic.student_alumnis.graduate_date) DESC
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
        DB::unprepared("DROP VIEW academic.student_alumnis_view");
    }
};

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
        $view = "CREATE OR REPLACE VIEW academic.teachers_view AS (
                    SELECT CONCAT(a.id, b.department_id, a.lesson_id, a.employee_id, b.grade_id, a.status_id) as seq, a.id, b.department_id, b.lesson_id, a.employee_id, b.grade_id, a.status_id, c.grade,
                    d.name AS department, e.name AS lesson, f.name as employee, g.name AS status
                    FROM academic.teachers a
                    JOIN academic.lesson_plans b ON b.lesson_id = a.lesson_id
                    JOIN academic.grades c ON c.id = b.grade_id
                    JOIN public.departments d ON d.id = b.department_id
                    JOIN academic.lessons e ON e.id = a.lesson_id
                    JOIN public.employees f ON f.id = a.employee_id
                    JOIN public.references g ON g.id = a.status_id
                    GROUP BY a.id, b.department_id, b.lesson_id, a.employee_id, b.grade_id, a.status_id, c.grade, d.name, e.name, f.name, g.name
                    ORDER BY a.id asc
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
        DB::unprepared("DROP VIEW academic.teachers_view");
    }
};

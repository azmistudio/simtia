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
        $view = "CREATE OR REPLACE VIEW academic.class_view AS (
                    SELECT a.id, a.schoolyear_id, a.grade_id, a.class, b.is_active AS stu_active, 
                    c.start_date, c.end_date, c.school_year, d.id AS department_id, UPPER(d.name) AS department, (a.capacity || '/' || COUNT(b.class_id)) AS capacity,
                    COUNT(b.class_id) AS occupied, UPPER(e.semester) AS semester, e.id AS semester_id, e.is_active AS sem_active, f.grade, c.is_active as scy_active,
                    CONCAT(a.id,a.schoolyear_id,a.grade_id,d.id,e.id) AS seq 
                    FROM academic.classes a
                    LEFT JOIN academic.students b ON b.class_id = a.id 
                    JOIN academic.grades f ON f.id = a.grade_id
                    JOIN public.departments d ON d.id = f.department_id
                    LEFT JOIN academic.semesters e ON e.department_id = d.id
                    JOIN academic.schoolyears c ON c.id = a.schoolyear_id
                    GROUP BY a.id, a.schoolyear_id, a.grade_id, a.class, b.is_active,
                    c.start_date, c.end_date, c.school_year, d.id, d.name, e.semester, e.id, e.is_active, f.grade, c.is_active
                    ORDER BY a.id ASC
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
        DB::unprepared("DROP VIEW academic.class_view");
    }
};

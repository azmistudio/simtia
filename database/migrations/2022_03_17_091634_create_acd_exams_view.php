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
        $view = "CREATE OR REPLACE VIEW academic.exams_view AS (
                    SELECT a.id, a.lesson_assessment_id, a.class_id, a.semester_id, b.student_id, d.student_no, d.name, concat(c.code, '_', a.id) AS code, b.score, e.avg_score, f.score AS final_score, f.remark
                    FROM academic.exams a
                    JOIN academic.exam_scores b ON b.exam_id = a.id
                    JOIN academic.lesson_exams c ON c.id = a.lesson_exam_id
                    JOIN academic.students d ON d.id = b.student_id
                    JOIN academic.avg_score_students e ON e.student_id = b.student_id AND e.lesson_assessment_id = a.lesson_assessment_id AND e.class_id = a.class_id AND e.semester_id = a.semester_id
                    LEFT JOIN academic.exam_score_finals f ON f.lesson_assessment_id = a.lesson_assessment_id AND f.class_id = a.class_id AND f.semester_id = a.semester_id AND f.student_id = b.student_id
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
        DB::unprepared("DROP VIEW academic.exams_view");
    }
};

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
        $procedure = "  CREATE OR REPLACE FUNCTION academic.sp_presence_lesson_student_presents (
                            IN p_student_id BIGINT,
                            IN p_class_id BIGINT,
                            IN p_semester_id BIGINT,
                            IN p_lesson_id BIGINT,
                            IN p_employee_id BIGINT,
                            IN p_month SMALLINT,
                            IN p_year SMALLINT,
                            IN p_logged TEXT
                        )
                        RETURNS void
                        LANGUAGE 'plpgsql'
                        AS $$
                            DECLARE 
                                check_exist int := 0;
                                r_presence int := 0;
                            BEGIN
                                SELECT count(academic.presence_lesson_students.id) FROM academic.presence_lesson_students 
                                INTO r_presence
                                INNER JOIN academic.presence_lessons ON academic.presence_lessons.id = academic.presence_lesson_students.presence_id
                                WHERE academic.presence_lesson_students.student_id = p_student_id AND academic.presence_lesson_students.presence = 0 AND academic.presence_lessons.lesson_id = p_lesson_id;
                                
                                SELECT count(id) FROM academic.presence_lesson_student_presents
                                INTO check_exist
                                WHERE student_id = p_student_id AND class_id = p_class_id AND semester_id = p_semester_id AND lesson_id = p_lesson_id AND employee_id = p_employee_id
                                AND month = p_month AND year = p_year;
                                
                                IF check_exist < 1 THEN
                                    INSERT INTO academic.presence_lesson_student_presents (student_id, class_id, semester_id, lesson_id, employee_id, month, year, present, logged, created_at)
                                    VALUES (p_student_id, p_class_id, p_semester_id, p_lesson_id, p_employee_id, p_month, p_year, r_presence, p_logged, NOW());
                                ELSE
                                    UPDATE academic.presence_lesson_student_presents SET present = r_presence, updated_at = NOW()
                                    WHERE student_id = p_student_id AND class_id = p_class_id AND semester_id = p_semester_id AND lesson_id = p_lesson_id AND employee_id = p_employee_id
                                    AND month = p_month AND year = p_year;
                                END IF;
                            END;
                        $$;";
        DB::unprepared($procedure);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared("DROP FUNCTION IF EXISTS academic.sp_presence_lesson_student_presents");    
    }
};

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
        $procedure = 'CREATE OR REPLACE FUNCTION academic.sp_students (
                            IN p_prospect_group_id integer, 
                            IN p_class_id integer, 
                            IN p_student_no text, 
                            IN p_prospect_student_id integer, 
                            IN p_remark_admission text, 
                            IN p_is_active smallint, 
                            IN p_logged text 
                        )
                        RETURNS void
                        LANGUAGE "plpgsql"
                        AS $$
                            BEGIN
                                INSERT INTO academic.students (
                                    student_no, name, surname, year_entry, prospect_student_group_id, class_id, tribe, religion, student_status, economic, gender, pob, dob, citizen, child_no, child_brother,
                                    child_status, child_brother_sum, child_step_sum, language, weight, height, blood, photo, address, distance, postal_code, phone, mobile, email, medical,
                                    father, mother, father_status, mother_status, is_father_died, is_mother_died, father_pob, mother_pob, father_dob, mother_dob, father_education, mother_education, father_job, mother_job, 
                                    father_income, mother_income, father_email, mother_email, parent_guardian, parent_address, father_mobile, mother_mobile, hobby, mail_address, remark, prospect_student_id, 
                                    remark_admission, mutation, is_active, logged, created_at
                                )
                                SELECT (SELECT p_student_no AS student_no), name, surname, year_entry, (SELECT p_prospect_group_id AS prospect_group_id), (SELECT p_class_id AS class_id), 
                                    tribe, religion, student_status, economic, gender, pob, dob, citizen, child_no, child_brother, child_status, child_brother_sum, child_step_sum, language, weight, height, blood, photo, address, 
                                    distance, postal_code, phone, mobile, email, medical, father, mother, father_status, mother_status, is_father_died, is_mother_died, father_pob, mother_pob, father_dob, mother_dob, father_education, 
                                    mother_education, father_job, mother_job, father_income, mother_income, father_email, mother_email, parent_guardian, parent_address, father_mobile, mother_mobile, hobby, mail_address, remark, 
                                    (SELECT p_prospect_student_id AS prospect_student_id), (SELECT p_remark_admission::text AS remark_admission), (SELECT NULL::integer AS mutation), (SELECT p_is_active AS is_active), (SELECT p_logged::text AS logged), (SELECT now() AS created_at)
                                FROM academic.prospect_students WHERE id = p_prospect_student_id;
                                -- 
                                INSERT INTO academic.column_students (
                                    student_id, column_id, type, "values", created_at
                                )
                                SELECT b.id, a.column_id, a.type, a.values, (SELECT now() AS created_at)
                                FROM academic.column_prospect_students a
                                JOIN academic.students b ON b.prospect_student_id = a.prospect_student_id
                                WHERE a.prospect_student_id = p_prospect_student_id;
                            END;
                        $$;';
        DB::unprepared($procedure);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared("DROP FUNCTION IF EXISTS academic.sp_students");    
    }
};

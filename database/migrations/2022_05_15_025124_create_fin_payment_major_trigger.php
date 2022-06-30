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
        $procedure = "
            CREATE OR REPLACE FUNCTION finance.payment_majors_bu() RETURNS trigger AS
            $$
            DECLARE 
                id_audit int := 0;
                p_source VARCHAR;
                p_user VARCHAR;
            BEGIN
                IF OLD.is_prospect = 0 THEN
                    p_source := 'major_jtt';
                ELSE 
                    p_source := 'major_jtt_prospect';
                END IF;
                -- get user
                SELECT name FROM public.employees WHERE email = NEW.logged INTO p_user;
                -- audit
                INSERT INTO finance.audits (department_id, bookyear_id, source, source_id, audit_date, employee, remark, logged, created_at)
                VALUES (OLD.department_id, OLD.bookyear_id, p_source, OLD.id, NOW(), p_user, NEW.reason, OLD.logged, NOW())
                RETURNING id INTO id_audit;
                -- audit payment major before
                INSERT INTO finance.audit_payment_majors (audit_id, is_prospect, is_status, prospect_student_id, student_id, major_id, total, is_paid, employee, remark, logged, created_at)
                VALUES (id_audit, OLD.is_prospect, 0, OLD.prospect_student_id, OLD.student_id, OLD.id, OLD.amount, OLD.is_paid, p_user, OLD.remark, OLD.logged, NOW());
                -- after
                INSERT INTO finance.audit_payment_majors (audit_id, is_prospect, is_status, prospect_student_id, student_id, major_id, total, is_paid, employee, remark, logged, created_at)
                VALUES (id_audit, NEW.is_prospect, 1, NEW.prospect_student_id, NEW.student_id, NEW.id, NEW.amount, NEW.is_paid, p_user, NEW.reason, NEW.logged, NOW());
                RETURN NEW;
            END;
            $$
            LANGUAGE 'plpgsql';

            DROP TRIGGER IF EXISTS payment_majors_bu ON finance.payment_majors;
            CREATE TRIGGER payment_majors_bu
                BEFORE UPDATE
                ON finance.payment_majors
                FOR EACH ROW
                EXECUTE PROCEDURE finance.payment_majors_bu();
            ";
        DB::unprepared($procedure);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $procedure = "
            DROP TRIGGER IF EXISTS payment_majors_bu ON finance.payment_majors;
            DROP FUNCTION IF EXISTS finance.payment_majors_bu;
        ";
        DB::unprepared($procedure);  
    }
};

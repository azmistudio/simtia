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
            CREATE OR REPLACE FUNCTION finance.receipt_voluntaries_bu() RETURNS trigger AS
            $$
            DECLARE 
                id_audit int := 0;
                p_source VARCHAR;
                p_user VARCHAR;
            BEGIN
                IF OLD.is_prospect = 0 THEN
                    p_source := 'receipt_skr';
                ELSE 
                    p_source := 'receipt_skr_prospect';
                END IF;
                -- get user
                SELECT name FROM public.employees WHERE email = NEW.logged INTO p_user;
                -- audit
                INSERT INTO finance.audits (department_id, bookyear_id, source, source_id, audit_date, employee, remark, logged, created_at)
                VALUES (OLD.department_id, OLD.bookyear_id, p_source, OLD.id, NOW(), p_user, NEW.reason, OLD.logged, NOW()) 
                RETURNING id INTO id_audit;
                -- before
                INSERT INTO finance.audit_receipt_voluntaries (audit_id, is_prospect, is_status, voluntary_id, journal_id, prospect_student_id, student_id, trans_date, total, employee, logged, created_at)
                VALUES (id_audit, OLD.is_prospect, 0, OLD.id, OLD.journal_id, OLD.prospect_student_id, OLD.student_id, OLD.trans_date, OLD.total, p_user, OLD.logged, NOW());
                -- after
                INSERT INTO finance.audit_receipt_voluntaries (audit_id, is_prospect, is_status, voluntary_id, journal_id, prospect_student_id, student_id, trans_date, total, employee, remark, logged, created_at)
                VALUES (id_audit, NEW.is_prospect, 1, OLD.id, NEW.journal_id, NEW.prospect_student_id, NEW.student_id, NEW.trans_date, NEW.total, p_user, NEW.reason, NEW.logged, NOW());
                RETURN NEW;
            END;
            $$
            LANGUAGE 'plpgsql';

            DROP TRIGGER IF EXISTS receipt_voluntaries_bu ON finance.receipt_voluntaries;
            CREATE TRIGGER receipt_voluntaries_bu
                BEFORE UPDATE
                ON finance.receipt_voluntaries
                FOR EACH ROW
                EXECUTE PROCEDURE finance.receipt_voluntaries_bu();
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
            DROP TRIGGER IF EXISTS receipt_voluntaries_bu ON finance.receipt_voluntaries;
            DROP FUNCTION IF EXISTS finance.receipt_voluntaries_bu;
        ";
        DB::unprepared($procedure);  
    }
};

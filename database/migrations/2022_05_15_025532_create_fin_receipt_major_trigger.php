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
            CREATE OR REPLACE FUNCTION finance.receipt_majors_bu() RETURNS trigger AS
            $$
            DECLARE 
                id_audit int := 0;
                p_source VARCHAR;
                p_user VARCHAR;
                p_department_id int := 0;
                p_bookyearid int := 0;
            BEGIN
                IF OLD.is_prospect = 0 THEN
                    p_source := 'receipt_jtt';
                ELSE 
                    p_source := 'receipt_jtt_prospect';
                END IF;
                -- get user
                SELECT name FROM public.employees WHERE email = NEW.logged INTO p_user;
                -- get dept and bookyear
                SELECT b.department_id, b.bookyear_id FROM finance.receipt_majors a
                JOIN finance.payment_majors b ON b.id = a.major_id
                WHERE a.id = OLD.id INTO p_department_id, p_bookyearid;
                -- audit
                INSERT INTO finance.audits (department_id, bookyear_id, source, source_id, audit_date, employee, remark, logged, created_at)
                VALUES (p_department_id, p_bookyearid, p_source, OLD.id, NOW(), p_user, NEW.reason, OLD.logged, NOW()) 
                RETURNING id INTO id_audit;
                -- audit payment major before
                INSERT INTO finance.audit_receipt_majors (audit_id, is_prospect, is_status, major_id, journal_id, trans_date, total, employee, logged, created_at)
                VALUES (id_audit, OLD.is_prospect, 0, OLD.major_id, OLD.journal_id, OLD.trans_date, OLD.total, p_user, OLD.logged, NOW());
                -- after
                INSERT INTO finance.audit_receipt_majors (audit_id, is_prospect, is_status, major_id, journal_id, trans_date, total, employee, remark, logged, created_at)
                VALUES (id_audit, NEW.is_prospect, 1, NEW.major_id, NEW.journal_id, NEW.trans_date, NEW.total, p_user, NEW.reason, NEW.logged, NOW());
                RETURN NEW;
            END;
            $$
            LANGUAGE 'plpgsql';

            DROP TRIGGER IF EXISTS receipt_majors_bu ON finance.receipt_majors;
            CREATE TRIGGER receipt_majors_bu
                BEFORE UPDATE
                ON finance.receipt_majors
                FOR EACH ROW
                EXECUTE PROCEDURE finance.receipt_majors_bu();
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
            DROP TRIGGER IF EXISTS receipt_majors_bu ON finance.receipt_majors;
            DROP FUNCTION IF EXISTS finance.receipt_majors_bu;
        ";
        DB::unprepared($procedure);  
    }
};

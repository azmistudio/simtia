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
            CREATE OR REPLACE FUNCTION finance.receipt_others_bu() RETURNS trigger AS
            $$
            DECLARE 
                id_audit int := 0;
                p_user VARCHAR;
            BEGIN
                -- get user
                SELECT name FROM public.employees WHERE email = NEW.logged INTO p_user;
                -- audit
                INSERT INTO finance.audits (department_id, bookyear_id, source, source_id, audit_date, employee, remark, logged, created_at)
                VALUES (OLD.department_id, OLD.bookyear_id, 'receipt_other', OLD.id, NOW(), p_user, NEW.reason, OLD.logged, NOW()) 
                RETURNING id INTO id_audit;
                -- before
                INSERT INTO finance.audit_receipt_others (audit_id, is_status, receipt_id, journal_id, trans_date, total, employee, logged, created_at)
                VALUES (id_audit, 0, OLD.id, OLD.journal_id, OLD.trans_date, OLD.total, p_user, OLD.logged, NOW());
                -- after
                INSERT INTO finance.audit_receipt_others (audit_id, is_status, receipt_id, journal_id, trans_date, total, employee, remark, logged, created_at)
                VALUES (id_audit, 1, OLD.id, NEW.journal_id, NEW.trans_date, NEW.total, p_user, NEW.reason, NEW.logged, NOW());
                RETURN NEW;
            END;
            $$
            LANGUAGE 'plpgsql';

            DROP TRIGGER IF EXISTS receipt_others_bu ON finance.receipt_others;
            CREATE TRIGGER receipt_others_bu
                BEFORE UPDATE
                ON finance.receipt_others
                FOR EACH ROW
                EXECUTE PROCEDURE finance.receipt_others_bu();
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
            DROP TRIGGER IF EXISTS receipt_others_bu ON finance.receipt_others;
            DROP FUNCTION IF EXISTS finance.receipt_others_bu;
        ";
        DB::unprepared($procedure); 
    }
};

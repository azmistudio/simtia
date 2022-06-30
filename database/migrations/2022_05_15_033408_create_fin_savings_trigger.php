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
            CREATE OR REPLACE FUNCTION finance.saving_bu() RETURNS trigger AS
            $$
            DECLARE 
                id_audit int := 0;
                p_user VARCHAR;
                p_source VARCHAR;
                p_department_id int := 0;
            BEGIN
                -- get user
                SELECT name FROM public.employees WHERE email = NEW.logged INTO p_user;
                -- get dept and bookyear
                SELECT b.department_id FROM finance.savings a
                JOIN finance.saving_types b ON b.id = a.saving_id
                WHERE a.id = OLD.id INTO p_department_id;
                -- get source
                IF OLD.transaction_type = 'credit' THEN
                    p_source := 'savingdeposit';
                ELSE 
                    p_source := 'savingwithdrawal';
                END IF;
                -- audit
                INSERT INTO finance.audits (department_id, bookyear_id, source, source_id, audit_date, employee, remark, logged, created_at)
                VALUES (p_department_id, OLD.bookyear_id, p_source, OLD.id, NOW(), p_user, NEW.reason, OLD.logged, NOW()) 
                RETURNING id INTO id_audit;
                -- before
                INSERT INTO finance.audit_savings (audit_id, is_status, employee_id, student_id, is_employee, saving_id, journal_id, debit, credit, transaction_type, employee, logged, created_at)
                VALUES (id_audit, 0, OLD.employee_id, OLD.student_id, OLD.is_employee, OLD.id, OLD.journal_id, OLD.debit, OLD.credit, OLD.transaction_type, p_user, OLD.logged, NOW());
                -- after
                INSERT INTO finance.audit_savings (audit_id, is_status, employee_id, student_id, is_employee, saving_id, journal_id, debit, credit, transaction_type, employee, remark, logged, created_at)
                VALUES (id_audit, 1, NEW.employee_id, NEW.student_id, NEW.is_employee, OLD.id, NEW.journal_id, NEW.debit, NEW.credit, NEW.transaction_type, p_user, NEW.reason, NEW.logged, NOW());
                RETURN NEW;
            END;
            $$
            LANGUAGE 'plpgsql';

            DROP TRIGGER IF EXISTS saving_bu ON finance.savings;
            CREATE TRIGGER saving_bu
                BEFORE UPDATE
                ON finance.savings
                FOR EACH ROW
                EXECUTE PROCEDURE finance.saving_bu();
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
            DROP TRIGGER IF EXISTS saving_bu ON finance.savings;
            DROP FUNCTION IF EXISTS finance.saving_bu;
        ";
        DB::unprepared($procedure); 
    }
};

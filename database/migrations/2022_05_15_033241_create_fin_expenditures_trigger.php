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
            CREATE OR REPLACE FUNCTION finance.expenditure_bu() RETURNS trigger AS
            $$
            DECLARE 
                id_audit int := 0;
                p_user VARCHAR;
                p_department_id int := 0;
                p_bookyearid int := 0;
            BEGIN
                -- get user
                SELECT name FROM public.employees WHERE email = NEW.logged INTO p_user;
                -- get dept and bookyear
                SELECT a.department_id, c.bookyear_id FROM finance.expenditures a
                JOIN finance.journals c ON c.id = a.journal_id
                WHERE a.id = OLD.id INTO p_department_id, p_bookyearid;
                -- audit
                INSERT INTO finance.audits (department_id, bookyear_id, source, source_id, audit_date, employee, remark, logged, created_at)
                VALUES (p_department_id, p_bookyearid, 'expense', OLD.id, NOW(), p_user, NEW.reason, OLD.logged, NOW()) 
                RETURNING id INTO id_audit;
                -- before
                INSERT INTO finance.audit_expenditures (audit_id, is_status, expenditure_id, purpose, requested_by, employee_id, student_id, requested_id, received_name, requested_name, trans_date, total, employee, logged, created_at)
                VALUES (id_audit, 0, OLD.id, OLD.purpose, OLD.requested_by, OLD.employee_id, OLD.student_id, OLD.requested_id, OLD.received_name, OLD.requested_name, OLD.trans_date, OLD.total, p_user, OLD.logged, NOW());
                -- after
                INSERT INTO finance.audit_expenditures (audit_id, is_status, expenditure_id, purpose, requested_by, employee_id, student_id, requested_id, received_name, requested_name, trans_date, total, employee, remark, logged, created_at)
                VALUES (id_audit, 1, OLD.id, NEW.purpose, NEW.requested_by, NEW.employee_id, NEW.student_id, NEW.requested_id, NEW.received_name, NEW.requested_name, NEW.trans_date, NEW.total, p_user, NEW.reason, NEW.logged, NOW());
                RETURN NEW;
            END;
            $$
            LANGUAGE 'plpgsql';

            DROP TRIGGER IF EXISTS expenditure_bu ON finance.expenditures;
            CREATE TRIGGER expenditure_bu
                BEFORE UPDATE
                ON finance.expenditures
                FOR EACH ROW
                EXECUTE PROCEDURE finance.expenditure_bu();
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
            DROP TRIGGER IF EXISTS expenditure_bu ON finance.expenditures;
            DROP FUNCTION IF EXISTS finance.expenditure_bu;
        ";
        DB::unprepared($procedure);   
    }
};

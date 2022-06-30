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
            CREATE OR REPLACE FUNCTION finance.begin_balances_bu() RETURNS trigger AS
            $$
            DECLARE 
                id_audit int := 0;
                p_user VARCHAR;
            BEGIN
                -- get user
                SELECT name FROM public.employees WHERE email = NEW.logged INTO p_user;
                -- audit
                INSERT INTO finance.audits (department_id, bookyear_id, source, source_id, audit_date, employee, remark, logged, created_at)
                VALUES (1, OLD.bookyear_id, 'begin_balance', OLD.id, NOW(), p_user, NEW.reason, OLD.logged, NOW()) 
                RETURNING id INTO id_audit;
                -- before
                INSERT INTO finance.audit_code_balances (audit_id, is_status, account_id, total, employee, logged, created_at)
                VALUES (id_audit, 0, OLD.account_id, OLD.total, p_user, OLD.logged, NOW());
                -- after
                INSERT INTO finance.audit_code_balances (audit_id, is_status, account_id, total, employee, remark, logged, created_at)
                VALUES (id_audit, 1, OLD.account_id, NEW.total, p_user, NEW.reason, NEW.logged, NOW());
                RETURN NEW;
            END;
            $$
            LANGUAGE 'plpgsql';

            DROP TRIGGER IF EXISTS begin_balances_bu ON finance.begin_balances;
            CREATE TRIGGER begin_balances_bu
                BEFORE UPDATE
                ON finance.begin_balances
                FOR EACH ROW
                EXECUTE PROCEDURE finance.begin_balances_bu();
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
            DROP TRIGGER IF EXISTS begin_balances_bu ON finance.begin_balances;
            DROP FUNCTION IF EXISTS finance.begin_balances_bu;
        ";
        DB::unprepared($procedure);   
    }
};

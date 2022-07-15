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
        $view = "CREATE OR REPLACE VIEW finance.journals_view AS (
                    SELECT a.id, a.journal_date, a.transaction, a.cash_no, a.bookyear_id, a.source, a.department_id, UPPER(d.name) AS department_name,
                    (
                        SELECT 
                        CASE
                            WHEN a.source = 'begin_balance' THEN SUM(b.debit)
                            WHEN a.source = 'receipt_jtt' THEN SUM(b.debit)
                            WHEN a.source = 'receipt_jtt_prospect' THEN SUM(b.debit)
                            WHEN a.source = 'receipt_skr' THEN SUM(b.debit)
                            WHEN a.source = 'receipt_skr_prospect' THEN SUM(b.debit)
                            WHEN a.source = 'receipt_others' THEN SUM(b.debit)
                            ELSE 0.00
                        END 
                        FROM finance.journal_details b
                        WHERE b.journal_id = a.id
                    ) AS debit,
                    (
                        SELECT 
                        CASE
                            WHEN a.source = 'begin_balance' THEN SUM(b.credit)
                            WHEN a.source = 'expense' THEN SUM(b.credit)
                            ELSE 0.00
                        END 
                        FROM finance.journal_details b
                        WHERE b.journal_id = a.id
                    ) AS credit,
                    INITCAP(c.name) AS employee, CONCAT(cash_no,' / ',TO_CHAR(journal_date,'DD-Mon-YYYY')) AS journal
                    FROM finance.journals a
                    JOIN public.employees c ON c.id = a.employee_id
                    JOIN public.departments d on d.id = a.department_id
                    WHERE a.source NOT IN ('major_jtt', 'major_jtt_prospect', 'savingdeposit', 'savingwithdrawal', 'journalvoucher', 'begin_balance')
                    UNION ALL
                    SELECT a.id, a.journal_date, a.transaction, a.cash_no, a.bookyear_id, a.source, a.department_id, UPPER(d.name) AS department_name, b.debit, b.credit, INITCAP(c.name) AS employee, CONCAT(cash_no,' / ',TO_CHAR(journal_date,'DD-Mon-YYYY')) AS journal
                    FROM finance.journals a
                    JOIN finance.savings b ON b.journal_id = a.id
                    JOIN public.employees c ON c.id = a.employee_id
                    JOIN public.departments d on d.id = a.department_id
                    WHERE a.source IN ('savingdeposit', 'savingwithdrawal') 
                    ORDER BY id DESC
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
        DB::unprepared("DROP VIEW finance.journals_view");
    }
};

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
        $procedure = "  CREATE OR REPLACE FUNCTION finance.fn_get_summary_ledger_account (
                            IN p_date_start DATE,
                            IN p_date_end DATE,
                            IN p_bookyear_id BIGINT
                        )
                        RETURNS TABLE(account_id bigint, code varchar, name varchar, parent bigint, beg_balance numeric, trx_debit numeric, trx_credit numeric, end_balance numeric)
                        LANGUAGE 'plpgsql'
                        AS $$
                            DECLARE 
                                var_r record;
                            BEGIN
                                FOR var_r IN(
                                    SELECT a.id, a.code, a.name, a.parent,
                                    COALESCE((
                                        SELECT SUM(d.beg_balance + d.balance) AS end_balance
                                        FROM 
                                        (
                                            SELECT a_.account_id, SUM(a_.total) AS beg_balance, COALESCE
                                            (
                                                (
                                                    SELECT SUM(b.debit) - SUM(b.credit) FROM finance.journal_details b 
                                                    JOIN finance.journals c ON c.id = b.journal_id
                                                    WHERE c.journal_date < p_date_start AND b.account_id = a_.account_id AND c.bookyear_id = p_bookyear_id and c.source <> 'begin_balance'
                                                ),
                                            0) AS balance
                                            FROM finance.begin_balances a_
                                            WHERE a_.bookyear_id = p_bookyear_id GROUP BY a_.account_id ORDER BY a_.account_id
                                        ) AS d 
                                        WHERE d.account_id = a.id
                                        GROUP BY d.account_id
                                    ), (SELECT * FROM finance.fn_get_beg_balance_account_parent(p_date_start,p_bookyear_id,a.id))) AS beg_balance,
                                    COALESCE((
                                        SELECT SUM(e.debit) FROM finance.journal_details e
                                        JOIN finance.journals f ON f.id = e.journal_id
                                        WHERE f.journal_date >= p_date_start AND f.journal_date <= p_date_end AND e.account_id = a.id AND f.bookyear_id = p_bookyear_id and f.source <> 'begin_balance'
                                    ),(SELECT * FROM finance.fn_get_sum_debit_account_parent(p_date_start,p_date_end,p_bookyear_id,a.id))) AS trx_debit,
                                    COALESCE((
                                        SELECT SUM(e.credit) FROM finance.journal_details e
                                        JOIN finance.journals f ON f.id = e.journal_id
                                        WHERE f.journal_date >= p_date_start AND f.journal_date <= p_date_end AND e.account_id = a.id AND f.bookyear_id = p_bookyear_id and f.source <> 'begin_balance'
                                    ),(SELECT * FROM finance.fn_get_sum_credit_account_parent(p_date_start,p_date_end,p_bookyear_id,a.id))) AS trx_credit
                                    FROM finance.codes a
                                    GROUP BY a.id, a.code, a.name, a.parent
                                    ORDER BY a.code ASC
                                ) LOOP
                                    account_id := var_r.id;
                                    code := var_r.code;
                                    name := var_r.name;
                                    parent := var_r.parent;
                                    beg_balance := var_r.beg_balance;
                                    trx_debit := var_r.trx_debit;
                                    trx_credit := var_r.trx_credit;
                                    end_balance := (var_r.beg_balance + var_r.trx_debit) - var_r.trx_credit;
                                    RETURN NEXT;
                                END LOOP;
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
        DB::unprepared("DROP FUNCTION IF EXISTS finance.fn_get_summary_ledger_account"); 
    }
};

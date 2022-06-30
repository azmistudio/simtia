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
        $procedure = "  CREATE OR REPLACE FUNCTION finance.fn_get_trial_balance (
                            IN p_date_start DATE,
                            IN p_date_end DATE,
                            IN p_bookyear_id BIGINT
                        )
                        RETURNS TABLE(account_id bigint, code varchar, name varchar, parent bigint, debit numeric, credit numeric)
                        LANGUAGE 'plpgsql'
                        AS $$
                            DECLARE 
                                var_r record;
                            BEGIN
                                FOR var_r IN(
                                    SELECT g.id, g.category_id, g.code, g.name, g.parent, ((g.beg_balance + g.debit) - g.credit) AS debit, 0.00 AS credit FROM (
                                    SELECT a.id, a.category_id, a.code, a.name, a.parent,
                                    ( 
                                        SELECT SUM(d.beg_balance + d.balance) AS end_balance FROM 
                                        ( SELECT a_.account_id, SUM(a_.total) AS beg_balance, COALESCE (
                                          ( 
                                                SELECT SUM(b.debit) - SUM(b.credit) FROM finance.journal_details b 
                                            JOIN finance.journals c ON c.id = b.journal_id
                                             WHERE c.journal_date < p_date_start AND b.account_id = a_.account_id AND c.bookyear_id = p_bookyear_id AND c.source <> 'begin_balance'
                                          ),
                                        0) AS balance
                                        FROM finance.begin_balances a_
                                        WHERE a_.bookyear_id = p_bookyear_id GROUP BY a_.account_id ORDER BY a_.account_id
                                    ) AS d 
                                    WHERE d.account_id = a.id
                                    GROUP BY d.account_id
                                    ) AS beg_balance,
                                    COALESCE(
                                    (
                                        SELECT SUM(e.debit) FROM finance.journal_details e
                                        JOIN finance.journals f ON f.id = e.journal_id
                                        WHERE f.journal_date >= p_date_start AND f.journal_date <= p_date_end AND e.account_id = a.id AND f.bookyear_id = p_bookyear_id AND f.source <> 'begin_balance'
                                    ),0) AS debit,
                                    COALESCE(
                                    (
                                        SELECT SUM(e.credit) FROM finance.journal_details e
                                        JOIN finance.journals f ON f.id = e.journal_id
                                        WHERE f.journal_date >= p_date_start AND f.journal_date <= p_date_end AND e.account_id = a.id AND f.bookyear_id = p_bookyear_id AND f.source <> 'begin_balance'
                                    ),0) AS credit
                                    FROM finance.codes a
                                    JOIN finance.code_categories b ON b.id = a.category_id
                                    WHERE a.id NOT IN (SELECT j.parent FROM finance.codes j WHERE j.parent <> 0)
                                    AND b.position = 'D'
                                    GROUP BY a.id, a.category_id, a.code, a.name, a.parent
                                    ORDER BY a.code
                                    ) AS G
                                    UNION ALL
                                    SELECT g.id, g.category_id, g.code, g.name, g.parent, 0.00 AS debit, ((g.beg_balance + g.credit) - g.debit) AS credit FROM (
                                    SELECT a.id, a.category_id, a.code, a.name, a.parent,
                                    ( 
                                        SELECT SUM(d.beg_balance + d.balance) AS end_balance FROM 
                                        ( SELECT a_.account_id, SUM(a_.total) AS beg_balance, COALESCE (
                                          ( 
                                            SELECT SUM(b.credit) - SUM(b.debit) FROM finance.journal_details b 
                                            JOIN finance.journals c ON c.id = b.journal_id
                                            WHERE c.journal_date < p_date_start AND b.account_id = a_.account_id AND c.bookyear_id = p_bookyear_id AND c.source <> 'begin_balance'
                                          ),
                                        0) AS balance
                                        FROM finance.begin_balances a_
                                        WHERE a_.bookyear_id = p_bookyear_id GROUP BY a_.account_id ORDER BY a_.account_id
                                    ) AS d 
                                    WHERE d.account_id = a.id
                                    GROUP BY d.account_id
                                    ) AS beg_balance,
                                    COALESCE(
                                    (
                                        SELECT SUM(e.debit) FROM finance.journal_details e
                                        JOIN finance.journals f ON f.id = e.journal_id
                                        WHERE f.journal_date >= p_date_start AND f.journal_date <= p_date_end AND e.account_id = a.id AND f.bookyear_id = p_bookyear_id AND f.source <> 'begin_balance'
                                    ),0) AS debit,
                                    COALESCE(
                                    (
                                        SELECT SUM(e.credit) FROM finance.journal_details e
                                        JOIN finance.journals f ON f.id = e.journal_id
                                        WHERE f.journal_date >= p_date_start AND f.journal_date <= p_date_end AND e.account_id = a.id AND f.bookyear_id = p_bookyear_id AND f.source <> 'begin_balance'
                                    ),0) AS credit
                                    FROM finance.codes a
                                    JOIN finance.code_categories b ON b.id = a.category_id
                                    WHERE a.id NOT IN (SELECT j.parent FROM finance.codes j WHERE j.parent <> 0)
                                    AND b.position = 'K'
                                    GROUP BY a.id, a.category_id, a.code, a.name, a.parent
                                    ORDER BY a.code
                                    ) AS G
                                    ORDER BY code                                    
                                ) LOOP
                                    account_id := var_r.id;
                                    code := var_r.code;
                                    name := var_r.name;
                                    parent := var_r.parent;
                                    debit := var_r.debit;
                                    credit := var_r.credit;
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
        DB::unprepared("DROP FUNCTION IF EXISTS finance.fn_get_trial_balance"); 
    }
};

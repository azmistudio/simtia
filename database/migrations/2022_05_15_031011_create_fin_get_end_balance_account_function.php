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
        $procedure = "  CREATE OR REPLACE FUNCTION finance.fn_get_end_balance_account (
                            IN p_date DATE,
                            IN p_bookyear_id BIGINT
                        )
                        RETURNS TABLE(account_id bigint, end_balance numeric)
                        LANGUAGE 'plpgsql'
                        AS $$
                            DECLARE 
                                var_r record;
                            BEGIN
                                FOR var_r IN(
                                    SELECT d.account_id, SUM(d.beg_balance + d.balance) AS end_balance
                                    FROM
                                    (
                                        SELECT a.account_id, SUM(a.total) AS beg_balance, COALESCE(
                                        (
                                            SELECT SUM(b.debit) - SUM(b.credit)
                                            FROM finance.journal_details b
                                            JOIN finance.journals c ON c.id = b.journal_id
                                            WHERE c.journal_date < p_date AND b.account_id = a.account_id AND c.bookyear_id = p_bookyear_id and c.source <> 'begin_balance'
                                            ),0
                                        ) AS balance
                                        FROM finance.begin_balances a
                                        WHERE a.bookyear_id = p_bookyear_id
                                        GROUP BY a.account_id ORDER BY a.account_id
                                    ) AS d
                                    GROUP BY d.account_id
                                ) LOOP
                                    account_id := var_r.account_id;
                                    end_balance := var_r.end_balance;
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
        DB::unprepared("DROP FUNCTION IF EXISTS finance.fn_get_end_balance_account");    
    }
};

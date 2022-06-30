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
        $procedure = "  CREATE OR REPLACE FUNCTION finance.fn_get_beg_balance_account_parent (
                            IN p_date DATE,
                            IN p_bookyear_id BIGINT,
                            IN p_parent BIGINT
                        )
                        RETURNS NUMERIC
                        LANGUAGE 'plpgsql'
                        AS $$
                            DECLARE 
                                v_balance NUMERIC;
                                v_beg_balance NUMERIC;
                                v_trx_balance NUMERIC;
                            BEGIN
                                SELECT COALESCE(SUM(a.total),0) AS beg_balance,
                                COALESCE((
                                    SELECT (SUM(a_.debit) - SUM(a_.credit))
                                    FROM finance.journal_details a_
                                    JOIN finance.journals b_ ON b_.id = a_.journal_id
                                    JOIN finance.codes c_ ON c_.id = a_.account_id
                                    WHERE b_.journal_date < p_date AND b_.bookyear_id = p_bookyear_id AND c_.parent = p_parent AND b_.source <> 'begin_balance'
                                ),0) AS trx_balance
                                FROM finance.begin_balances a
                                JOIN finance.codes b ON b.id = a.account_id
                                WHERE a.bookyear_id = p_bookyear_id AND b.parent = p_parent
                                INTO v_beg_balance, v_trx_balance;
                                v_balance := v_beg_balance + v_trx_balance;
                                RETURN v_balance;
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
        DB::unprepared("DROP FUNCTION IF EXISTS finance.fn_get_beg_balance_account_parent");
    }
};

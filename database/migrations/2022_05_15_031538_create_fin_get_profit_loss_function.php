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
        $procedure = "  CREATE OR REPLACE FUNCTION finance.fn_get_profit_loss (
                            IN p_category_id BIGINT,
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
                                    SELECT a.id, a.category_id, a.code, a.name, a.parent,
                                    COALESCE((
                                        SELECT SUM(c.credit) - SUM(c.debit) FROM finance.journal_details c
                                        JOIN finance.journals d ON d.id = c.journal_id
                                        WHERE d.journal_date >= p_date_start AND d.journal_date <= p_date_end 
                                        AND c.account_id = a.id AND d.bookyear_id = p_bookyear_id
                                    ),(SELECT * FROM finance.fn_get_trx_credit_account_parent(p_date_start,p_date_end,p_bookyear_id,a.id))) AS debit,
                                    COALESCE((
                                        SELECT SUM(c.debit) - SUM(c.credit) FROM finance.journal_details c
                                        JOIN finance.journals d ON d.id = c.journal_id
                                        WHERE d.journal_date >= p_date_start AND d.journal_date <= p_date_end 
                                        AND c.account_id = a.id AND d.bookyear_id = p_bookyear_id
                                    ),(SELECT * FROM finance.fn_get_trx_debit_account_parent(p_date_start,p_date_end,p_bookyear_id,a.id))) AS credit
                                    FROM finance.codes a
                                    WHERE a.category_id = p_category_id
                                    GROUP BY a.id, a.category_id, a.code, a.name, a.parent
                                    ORDER BY a.code
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
        DB::unprepared("DROP FUNCTION IF EXISTS finance.fn_get_profit_loss"); 
    }
};

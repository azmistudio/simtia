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
        $procedure = "  CREATE OR REPLACE FUNCTION finance.fn_get_cashflow_receivables_reduce (
                            IN p_date_start DATE,
                            IN p_date_end DATE,
                            IN p_bookyear_id BIGINT
                        )
                        RETURNS TABLE(value numeric)
                        LANGUAGE 'plpgsql'
                        AS $$
                            DECLARE 
                                var_r record;
                            BEGIN
                                FOR var_r IN(
                                    SELECT SUM(a.debit - a.credit) AS value
                                    FROM finance.journal_details a, finance.codes b, finance.code_categories c
                                    WHERE a.account_id = b.id AND b.category_id = c.id AND c.category = 'HARTA' AND a.debit > 0 
                                    AND a.journal_id IN (
                                        SELECT d.journal_id
                                        FROM finance.journal_details d, finance.journals e, finance.codes f, finance.code_categories g
                                        WHERE e.source = 'journalvoucher' 
                                        AND d.journal_id = e.id
                                        AND d.account_id = f.id
                                        AND f.category_id = g.id
                                        AND e.journal_date BETWEEN p_date_start AND p_date_end AND e.bookyear_id = p_bookyear_id
                                        AND g.category = 'HARTA' AND f.code LIKE '1-2%' AND d.credit > 0
                                    )
                                    GROUP BY b.name
                                ) LOOP
                                    value := var_r.value;
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
        DB::unprepared("DROP FUNCTION IF EXISTS finance.fn_get_cashflow_receivables_reduce"); 
    }
};

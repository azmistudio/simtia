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
        $procedure = "  CREATE OR REPLACE FUNCTION finance.fn_get_cashflow_expense (
                            IN p_date_start DATE,
                            IN p_date_end DATE,
                            IN p_bookyear_id BIGINT
                        )
                        RETURNS NUMERIC
                        LANGUAGE 'plpgsql'
                        AS $$
                            DECLARE 
                                v_value NUMERIC;
                            BEGIN
                                SELECT SUM(a.debit - a.credit) AS value FROM 
                                finance.journal_details a
                                JOIN finance.codes b ON b.id = a.account_id
                                JOIN finance.code_categories c ON c.id = b.category_id
                                WHERE c.category = 'HARTA' AND a.journal_id IN (
                                    SELECT d.journal_id FROM finance.journal_details d
                                    JOIN finance.journals e ON e.id = d.journal_id
                                    JOIN finance.codes f ON f.id = d.account_id
                                    JOIN finance.code_categories g ON g.id = f.category_id
                                    WHERE e.journal_date BETWEEN p_date_start AND p_date_end AND e.bookyear_id = p_bookyear_id AND g.category = 'BIAYA' AND f.code LIKE '5-1%'
                                ) INTO v_value;
                                RETURN v_value;
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
        DB::unprepared("DROP FUNCTION IF EXISTS finance.fn_get_cashflow_expense"); 
    }
};

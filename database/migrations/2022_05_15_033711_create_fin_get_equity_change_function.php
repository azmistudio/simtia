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
        $procedure = "  CREATE OR REPLACE FUNCTION finance.fn_get_equity_change_periods (
                            IN p_date_start DATE,
                            IN p_date_end DATE,
                            IN p_bookyear_id BIGINT
                        )
                        RETURNS TABLE(subject varchar, value numeric)
                        LANGUAGE 'plpgsql'
                        AS $$
                            DECLARE 
                                var_r record;
                                v_start_date DATE;
                            BEGIN
                                SELECT date_trunc('MONTH', p_date_end::DATE) INTO v_start_date;
                                FOR var_r IN(
                                    SELECT 'equity_begin' AS subject,
                                    (SELECT fn_get_equity_change FROM finance.fn_get_equity_change(p_date_start,p_date_end,p_bookyear_id)) AS value
                                    UNION ALL
                                    SELECT 'profit_loss' AS subject,
                                    COALESCE((
                                        SELECT SUM(
                                        (
                                            SELECT SUM(a.credit - a.debit) AS income
                                            FROM finance.journal_details a
                                            JOIN finance.codes b ON b.id = a.account_id
                                            JOIN finance.code_categories c ON c.id = b.category_id
                                            JOIN finance.journals d ON d.id = a.journal_id
                                            WHERE d.bookyear_id = p_bookyear_id AND d.journal_date BETWEEN v_start_date AND p_date_end
                                            AND c.category = 'PENDAPATAN'
                                        ) - (
                                            SELECT SUM(a.debit - a.credit) AS income
                                            FROM finance.journal_details a
                                            JOIN finance.codes b ON b.id = a.account_id
                                            JOIN finance.code_categories c ON c.id = b.category_id
                                            JOIN finance.journals d ON d.id = a.journal_id
                                            WHERE d.bookyear_id = p_bookyear_id AND d.journal_date BETWEEN v_start_date AND p_date_end
                                            AND c.category = 'BIAYA' )
                                        )
                                    ),0) AS value
                                    UNION ALL
                                    SELECT 'investment' AS subject,
                                    COALESCE((
                                        SELECT SUM(a.credit - a.debit) AS income
                                        FROM finance.journal_details a
                                        JOIN finance.codes b ON b.id = a.account_id
                                        JOIN finance.code_categories c ON c.id = b.category_id
                                        JOIN finance.journals d ON d.id = a.journal_id
                                        WHERE d.bookyear_id = p_bookyear_id AND d.journal_date BETWEEN v_start_date AND p_date_end
                                        AND c.category = 'MODAL' AND a.credit > 0
                                    ),0) AS value
                                    UNION ALL
                                    SELECT 'withdrawal' AS subject,
                                    COALESCE((
                                        SELECT SUM(a.debit - a.credit) AS income
                                        FROM finance.journal_details a
                                        JOIN finance.codes b ON b.id = a.account_id
                                        JOIN finance.code_categories c ON c.id = b.category_id
                                        JOIN finance.journals d ON d.id = a.journal_id
                                        WHERE d.bookyear_id = p_bookyear_id AND d.journal_date BETWEEN v_start_date AND p_date_end
                                        AND c.category = 'MODAL' AND a.debit > 0
                                    ),0) AS value
                                ) LOOP
                                    subject := var_r.subject;
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
        DB::unprepared("DROP FUNCTION IF EXISTS finance.fn_get_equity_change_periods"); 
    }
};

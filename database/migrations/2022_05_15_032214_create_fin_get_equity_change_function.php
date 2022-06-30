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
        $procedure = "  CREATE OR REPLACE FUNCTION finance.fn_get_equity_change (
                            IN p_date_start DATE,
                            IN p_date_end DATE,
                            IN p_bookyear_id BIGINT
                        )
                        RETURNS NUMERIC
                        LANGUAGE 'plpgsql'
                        AS $$
                            DECLARE 
                                v_value NUMERIC;
                                v_end_date DATE;
                            BEGIN
                                SELECT date_trunc('MONTH', p_date_end::date) - INTERVAL '1 DAY' INTO v_end_date;
                                SELECT SUM(VALUE) FROM (
                                    SELECT 'equity_begin' AS subject,
                                    COALESCE((
                                        SELECT SUM(b.total) AS income
                                        FROM finance.codes a
                                        JOIN finance.begin_balances b ON b.account_id = a.id
                                        WHERE a.category_id = 3 AND a.parent > 0 AND b.bookyear_id = p_bookyear_id
                                    ),0) AS value
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
                                            WHERE d.bookyear_id = p_bookyear_id AND d.journal_date BETWEEN p_date_start AND v_end_date
                                            AND c.category = 'PENDAPATAN'
                                        ) - (
                                            SELECT SUM(a.debit - a.credit) AS income
                                            FROM finance.journal_details a
                                            JOIN finance.codes b ON b.id = a.account_id
                                            JOIN finance.code_categories c ON c.id = b.category_id
                                            JOIN finance.journals d ON d.id = a.journal_id
                                            WHERE d.bookyear_id = p_bookyear_id AND d.journal_date BETWEEN p_date_start AND v_end_date
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
                                        WHERE d.bookyear_id = p_bookyear_id AND d.journal_date BETWEEN p_date_start AND v_end_date
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
                                        WHERE d.bookyear_id = p_bookyear_id AND d.journal_date BETWEEN p_date_start AND v_end_date
                                        AND c.category = 'MODAL' AND a.debit > 0
                                    ),0) AS value
                                ) AS x INTO v_value;
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
        DB::unprepared("DROP FUNCTION IF EXISTS finance.fn_get_equity_change"); 
    }
};

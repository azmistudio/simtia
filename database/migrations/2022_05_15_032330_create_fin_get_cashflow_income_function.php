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
        $procedure = "  CREATE OR REPLACE FUNCTION finance.fn_get_cashflow_income (
                            IN p_date_start DATE,
                            IN p_date_end DATE,
                            IN p_bookyear_id BIGINT
                        )
                        RETURNS TABLE(id bigint, category_id bigint, code varchar, name varchar, value numeric)
                        LANGUAGE 'plpgsql'
                        AS $$
                            DECLARE 
                                var_r record;
                            BEGIN
                                FOR var_r IN(
                                    SELECT x.* FROM (
                                        SELECT a.id, a.category_id, a.code, a.name,
                                        COALESCE((
                                            SELECT SUM(c.debit - c.credit) 
                                            FROM finance.journal_details c
                                            JOIN finance.journals d ON d.id = c.journal_id
                                            JOIN finance.codes e ON e.id = c.account_id
                                            JOIN finance.code_categories f ON f.id = e.category_id
                                            WHERE f.category = 'HARTA' AND e.code LIKE '1-1%' AND
                                            c.journal_id IN (
                                                SELECT g.id FROM finance.journals g, finance.receipt_majors h, finance.payment_majors i, finance.receipt_types j
                                                WHERE g.id = h.journal_id AND h.major_id = i.id AND i.receipt_id = j.id AND j.receipt_account = a.id
                                                AND g.journal_date BETWEEN p_date_start AND p_date_end AND g.bookyear_id = p_bookyear_id 
                                            )
                                        ),0) AS value
                                        FROM finance.codes a
                                        JOIN finance.code_categories b ON b.id = a.category_id
                                        WHERE a.id NOT IN (SELECT parent FROM finance.codes WHERE parent <> 0) 
                                        AND b.category = 'PENDAPATAN'
                                        ORDER BY a.code
                                    ) AS x WHERE x.value <> 0
                                    UNION ALL
                                    SELECT y.* FROM (
                                        SELECT a.id, a.category_id, a.code, a.name,
                                        COALESCE((
                                            SELECT SUM(c.debit - c.credit) 
                                            FROM finance.journal_details c
                                            JOIN finance.journals d ON d.id = c.journal_id
                                            JOIN finance.codes e ON e.id = c.account_id
                                            JOIN finance.code_categories f ON f.id = e.category_id
                                            WHERE f.category = 'HARTA' AND e.code LIKE '1-1%' AND 
                                            c.journal_id IN (
                                                SELECT g.id FROM finance.journals g, finance.receipt_voluntaries h, finance.receipt_types j
                                                WHERE g.id = h.journal_id AND h.receipt_id = j.id AND j.receipt_account = a.id
                                                AND g.journal_date BETWEEN p_date_start AND p_date_end AND g.bookyear_id = p_bookyear_id 
                                            )
                                        ),0) AS value
                                        FROM finance.codes a
                                        JOIN finance.code_categories b ON b.id = a.category_id
                                        WHERE a.id NOT IN (SELECT parent FROM finance.codes WHERE parent <> 0) 
                                        AND b.category = 'PENDAPATAN'
                                        ORDER BY a.code
                                    ) AS y WHERE y.value <> 0
                                    UNION ALL
                                    SELECT z.* FROM (
                                        SELECT a.id, a.category_id, a.code, a.name,
                                        COALESCE((
                                            SELECT SUM(c.debit - c.credit) 
                                            FROM finance.journal_details c
                                            JOIN finance.journals d ON d.id = c.journal_id
                                            JOIN finance.codes e ON e.id = c.account_id
                                            JOIN finance.code_categories f ON f.id = e.category_id
                                            WHERE f.category = 'HARTA' AND e.code LIKE '1-1%' AND
                                            c.journal_id IN (
                                                SELECT g.id FROM finance.journals g, finance.receipt_others h, finance.receipt_types j
                                                WHERE g.id = h.journal_id AND h.receipt_id = j.id AND j.receipt_account = a.id
                                                AND g.journal_date BETWEEN p_date_start AND p_date_end AND g.bookyear_id = p_bookyear_id 
                                            )
                                        ),0) AS value
                                        FROM finance.codes a
                                        JOIN finance.code_categories b ON b.id = a.category_id
                                        WHERE a.id NOT IN (SELECT parent FROM finance.codes WHERE parent <> 0) 
                                        AND b.category = 'PENDAPATAN'
                                        ORDER BY a.code
                                    ) AS z WHERE z.value <> 0
                                ) LOOP
                                    id := var_r.id;
                                    category_id := var_r.category_id;
                                    code := var_r.code;
                                    name := var_r.name;
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
        DB::unprepared("DROP FUNCTION IF EXISTS finance.fn_get_cashflow_income"); 
    }
};

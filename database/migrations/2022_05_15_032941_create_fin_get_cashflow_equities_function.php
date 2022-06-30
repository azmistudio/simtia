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
        $procedure = "  CREATE OR REPLACE FUNCTION finance.fn_get_cashflow_equities (
                            IN p_date_start DATE,
                            IN p_date_end DATE,
                            IN p_bookyear_id BIGINT
                        )
                        RETURNS TABLE(name varchar, value numeric)
                        LANGUAGE 'plpgsql'
                        AS $$
                            DECLARE 
                                var_r record;
                            BEGIN
                                FOR var_r IN(
                                    SELECT j.name, SUM(a.debit - a.credit) AS value
                                    FROM finance.journal_details a, finance.codes b, finance.code_categories c, finance.journals d,
                                    (
                                        SELECT e.journal_id, g.name 
                                        FROM finance.journal_details e, finance.journals f, finance.codes g, finance.code_categories h
                                        WHERE f.source = 'journalvoucher' AND e.journal_id = f.id AND e.account_id = g.id AND h.id = g.category_id
                                        AND f.journal_date BETWEEN p_date_start AND p_date_end AND f.bookyear_id = p_bookyear_id AND h.category = 'MODAL' AND e.credit > 0
                                    ) AS j
                                    WHERE a.account_id = b.id AND b.category_id = c.id AND a.journal_id = d.id and j.journal_id = a.journal_id AND a.debit > 0 AND c.category = 'HARTA'
                                    GROUP BY j.name
                                ) LOOP
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
        DB::unprepared("DROP FUNCTION IF EXISTS finance.fn_get_cashflow_equities"); 
    }
};

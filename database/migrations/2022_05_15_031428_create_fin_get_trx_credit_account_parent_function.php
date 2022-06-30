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
        $procedure = "  CREATE OR REPLACE FUNCTION finance.fn_get_trx_credit_account_parent (
                            IN p_date_start DATE,
                            IN p_date_end DATE,
                            IN p_bookyear_id BIGINT,
                            IN p_parent BIGINT
                        )
                        RETURNS NUMERIC
                        LANGUAGE 'plpgsql'
                        AS $$
                            DECLARE 
                                v_value NUMERIC;
                            BEGIN
                                SELECT COALESCE(SUM(a.credit) - SUM(a.debit),0) FROM finance.journal_details a
                                JOIN finance.journals b ON b.id = a.journal_id
                                JOIN finance.codes c ON c.id = a.account_id
                                WHERE b.journal_date >= p_date_start AND b.journal_date <= p_date_end AND b.bookyear_id = p_bookyear_id AND c.parent = p_parent
                                INTO v_value;
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
        DB::unprepared("DROP FUNCTION IF EXISTS finance.fn_get_trx_credit_account_parent");
    }
};

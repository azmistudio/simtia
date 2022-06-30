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
        $procedure = "  CREATE OR REPLACE FUNCTION finance.sp_account_code_balance (
                            IN p_account_id BIGINT,
                            IN p_logged TEXT
                        )
                        RETURNS void
                        LANGUAGE 'plpgsql'
                        AS $$
                            DECLARE 
                                v_old_balance numeric := 0;
                                v_new_balance numeric := 0;
                                v_parent integer := 0;
                            BEGIN
                                SELECT balance, parent FROM finance.codes WHERE id = p_account_id INTO v_old_balance, v_parent;
                                IF v_parent > 0 THEN
                                    SELECT SUM(balance) FROM finance.codes WHERE parent = v_parent INTO v_new_balance;
                                    UPDATE finance.codes SET 
                                        balance = v_new_balance,
                                        logged = p_logged,
                                        updated_at = NOW()
                                    WHERE id = v_parent;
                                END IF;
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
        DB::unprepared("DROP FUNCTION IF EXISTS finance.sp_account_code_balance");  
    }
};

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
        $procedure = "
            CREATE OR REPLACE FUNCTION academic.fn_admissions_ad() RETURNS trigger AS
            $$
            BEGIN
                IF OLD.is_active = 1 THEN
                    UPDATE academic.admissions SET is_active = 1
                    WHERE id = (SELECT id FROM academic.admissions a WHERE a.id <> OLD.id AND a.department_id = OLD.department_id ORDER BY a.id DESC LIMIT 1);
                END IF;
                RETURN NEW;
            END;
            $$
            LANGUAGE 'plpgsql';

            DROP TRIGGER IF EXISTS admissions_ad ON academic.admissions;
            CREATE TRIGGER admissions_ad
                AFTER DELETE
                ON academic.admissions
                FOR EACH ROW
                EXECUTE PROCEDURE academic.fn_admissions_ad();";

        DB::unprepared($procedure);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared("
            DROP TRIGGER IF EXISTS admissions_ad ON academic.admissions;
            DROP FUNCTION IF EXISTS academic.fn_admissions_ad;
        ");    
    }
};

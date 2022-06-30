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
            CREATE OR REPLACE FUNCTION academic.fn_schoolyears_ai() RETURNS trigger AS
            $$
            BEGIN
                UPDATE academic.schoolyears SET is_active = 2 
                WHERE id <> NEW.id AND department_id = NEW.department_id;
                RETURN NEW;
            END;
            $$
            LANGUAGE 'plpgsql';

            DROP TRIGGER IF EXISTS schoolyears_ai ON academic.schoolyears;
            CREATE TRIGGER schoolyears_ai
                AFTER INSERT
                ON academic.schoolyears
                FOR EACH ROW
                EXECUTE PROCEDURE academic.fn_schoolyears_ai();

            CREATE OR REPLACE FUNCTION academic.fn_schoolyears_ad() RETURNS trigger AS
            $$
            BEGIN
                IF OLD.is_active = 1 THEN
                    UPDATE academic.schoolyears SET is_active = 1
                    WHERE id = (SELECT id FROM academic.schoolyears a WHERE a.id <> OLD.id AND a.department_id = OLD.department_id ORDER BY a.id DESC LIMIT 1);
                END IF;
                RETURN NEW;
            END;
            $$
            LANGUAGE 'plpgsql';

            DROP TRIGGER IF EXISTS schoolyears_ad ON academic.schoolyears;
            CREATE TRIGGER schoolyears_ad
                AFTER DELETE
                ON academic.schoolyears
                FOR EACH ROW
                EXECUTE PROCEDURE academic.fn_schoolyears_ad();";

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
            DROP TRIGGER IF EXISTS schoolyears_ad ON academic.schoolyears;
            DROP FUNCTION IF EXISTS academic.fn_schoolyears_ad;
            DROP TRIGGER IF EXISTS schoolyears_ai ON academic.schoolyears;
            DROP FUNCTION IF EXISTS academic.fn_schoolyears_ai;
        ");    
    }
};

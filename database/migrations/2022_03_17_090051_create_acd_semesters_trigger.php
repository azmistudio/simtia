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
            CREATE OR REPLACE FUNCTION academic.fn_semester_ai() RETURNS trigger AS
            $$
            BEGIN
                UPDATE academic.semesters SET is_active = 2 
                WHERE id <> NEW.id AND department_id = NEW.department_id;
                RETURN NEW;
            END;
            $$
            LANGUAGE 'plpgsql';

            DROP TRIGGER IF EXISTS semesters_ai ON academic.semesters;
            CREATE TRIGGER semesters_ai
                AFTER INSERT
                ON academic.semesters
                FOR EACH ROW
                EXECUTE PROCEDURE academic.fn_semester_ai();

            CREATE OR REPLACE FUNCTION academic.fn_semester_ad() RETURNS trigger AS
            $$
            BEGIN
                IF OLD.is_active = 1 THEN
                    UPDATE academic.semesters SET is_active = 1
                    WHERE id = (SELECT id FROM academic.semesters a WHERE a.id <> OLD.id AND a.department_id = OLD.department_id ORDER BY a.id DESC LIMIT 1);
                END IF;
                RETURN NEW;
            END;
            $$
            LANGUAGE 'plpgsql';

            DROP TRIGGER IF EXISTS semesters_ad ON academic.semesters;
            CREATE TRIGGER semesters_ad
                AFTER DELETE
                ON academic.semesters
                FOR EACH ROW
                EXECUTE PROCEDURE academic.fn_semester_ad();";

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
            DROP TRIGGER IF EXISTS semesters_ad ON academic.semesters;
            DROP FUNCTION IF EXISTS academic.fn_semester_ad;
            DROP TRIGGER IF EXISTS semesters_ai ON academic.semesters;
            DROP FUNCTION IF EXISTS academic.fn_semester_ai;
        ");    
    }
};

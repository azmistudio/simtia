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
            CREATE OR REPLACE FUNCTION academic.fn_presence_daily_bi() RETURNS trigger AS
            $$
            DECLARE 
                check_exist int := 0;
            BEGIN
                SELECT count(id) FROM academic.presence_dailies 
                INTO check_exist
                WHERE
                (
                    ((start_date BETWEEN NEW.start_date AND NEW.end_date) OR (end_date BETWEEN NEW.start_date AND NEW.end_date))
                    OR
                    ((NEW.start_date BETWEEN start_date AND end_date) OR (NEW.end_date BETWEEN start_date AND end_date))
                )
                AND class_id = NEW.class_id AND semester_id = NEW.semester_id;
                
                IF check_exist < 1 THEN
                    RAISE NOTICE 'OK';
                ELSE
                    RAISE EXCEPTION 'Ada Presensi antara tanggal % s.d %', to_char(NEW.start_date, 'DD/MM/YYYY'), to_char(NEW.end_date, 'DD/MM/YYYY');
                END IF;
                RETURN NEW;
            END;
            $$
            LANGUAGE 'plpgsql';

            DROP TRIGGER IF EXISTS presence_daily_bi ON academic.presence_dailies;
            CREATE TRIGGER presence_daily_bi
                BEFORE INSERT
                ON academic.presence_dailies
                FOR EACH ROW
                EXECUTE PROCEDURE academic.fn_presence_daily_bi();

            CREATE OR REPLACE FUNCTION academic.fn_presence_daily_bu() RETURNS trigger AS
            $$
            DECLARE 
                check_exist int := 0;
            BEGIN
                SELECT count(id) FROM academic.presence_dailies 
                INTO check_exist
                WHERE
                (
                    ((start_date BETWEEN NEW.start_date AND NEW.end_date) OR (end_date BETWEEN NEW.start_date AND NEW.end_date))
                    OR
                    ((NEW.start_date BETWEEN start_date AND end_date) OR (NEW.end_date BETWEEN start_date AND end_date))
                )
                AND class_id = NEW.class_id AND semester_id = NEW.semester_id AND NEW.id <> OLD.id;
                
                IF check_exist < 1 THEN
                    RAISE NOTICE 'OK';
                ELSE
                    RAISE EXCEPTION 'Ada Presensi antara tanggal % s.d %', to_char(NEW.start_date, 'DD/MM/YYYY'), to_char(NEW.end_date, 'DD/MM/YYYY');
                END IF;
                RETURN NEW;
            END;
            $$
            LANGUAGE 'plpgsql';

            DROP TRIGGER IF EXISTS presence_daily_bu ON academic.presence_dailies;
            CREATE TRIGGER presence_daily_bu
                BEFORE UPDATE
                ON academic.presence_dailies
                FOR EACH ROW
                EXECUTE PROCEDURE academic.fn_presence_daily_bu();
            ";
        DB::unprepared($procedure);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $procedure = "
            DROP TRIGGER IF EXISTS presence_daily_bi ON academic.presence_dailies;
            DROP FUNCTION IF EXISTS academic.fn_presence_daily_bi;
            DROP TRIGGER IF EXISTS presence_daily_bu ON academic.presence_dailies;
            DROP FUNCTION IF EXISTS academic.fn_presence_daily_bu;
        ";
        DB::unprepared($procedure);   
    }
};

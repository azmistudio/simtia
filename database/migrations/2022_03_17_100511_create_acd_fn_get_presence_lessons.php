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
        $procedure = "  CREATE OR REPLACE FUNCTION academic.fn_get_presence_lessons (
                            IN p_day_val INT,
                            IN p_date_start DATE,
                            IN p_class_id BIGINT,
                            IN p_lesson_id BIGINT,
                            IN p_employee_id BIGINT
                        )
                        RETURNS TABLE(day varchar, date_gen date, day_val int, week_val int)
                        LANGUAGE 'plpgsql'
                        AS $$
                            DECLARE 
                                var_r record;
                            BEGIN
                                FOR var_r IN(
                                    SELECT CASE 
                                        WHEN p_day_val = 1 THEN 'Senin'
                                        WHEN p_day_val = 2 THEN 'Selasa'
                                        WHEN p_day_val = 3 THEN 'Rabu'
                                        WHEN p_day_val = 4 THEN 'Kamis'
                                        WHEN p_day_val = 5 THEN 'Jum`at'
                                        WHEN p_day_val = 6 THEN 'Sabtu'
                                        WHEN p_day_val = 7 THEN 'Ahad'
                                    END AS day,
                                    *, EXTRACT(DAY FROM src.date_gen)::int AS day_val,
                                    CEIL(EXTRACT(DAY FROM src.date_gen) / 7)::int AS week_val
                                    FROM 
                                    (
                                        SELECT generate_series(date_trunc('year', NOW()), date_trunc('year', NOW() + INTERVAL '1 year'), INTERVAL '1 day'):: DATE AS date_gen
                                    ) AS src
                                    WHERE 
                                    EXTRACT ('dow' FROM src.date_gen) = p_day_val 
                                    AND 
                                    CEIL(EXTRACT(DAY FROM src.date_gen) / 7)::INT 
                                    IN (1,2,3,4,5)
                                    AND src.date_gen >= p_date_start AND src.date_gen <= 
                                    (
                                        CASE WHEN p_date_start > NOW() THEN p_date_start + INTERVAL '1 month' ELSE NOW() END
                                    ) 
                                    AND src.date_gen NOT IN (SELECT DATE FROM academic.presence_lessons WHERE class_id = p_class_id AND lesson_id = p_lesson_id AND employee_id = p_employee_id)
                                ) LOOP
                                    day := var_r.day;
                                    date_gen := var_r.date_gen;
                                    day_val := var_r.day_val;
                                    week_val := var_r.week_val;
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
        DB::unprepared("DROP FUNCTION IF EXISTS academic.fn_get_presence_lessons"); 
    }
};

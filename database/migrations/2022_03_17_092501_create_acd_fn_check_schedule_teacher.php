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
        $procedure = "  CREATE OR REPLACE FUNCTION academic.fn_check_schedule_teacher (
                            IN p_infos TEXT,
                            IN p_day SMALLINT,
                            IN p_employee SMALLINT,
                            IN p_time TEXT
                        )
                        RETURNS TABLE(description character varying, name character varying, lesson character varying, class character varying, dept bigint, day smallint, from_time smallint, to_time integer)
                        LANGUAGE 'plpgsql'
                        AS $$
                            DECLARE 
                                ids INT[];
                            BEGIN
                                ids = string_to_array(p_infos, ',');
                                RETURN QUERY EXECUTE
                                    'SELECT academic.lesson_schedule_infos.description, public.employees.name, academic.lessons.name AS lesson, academic.classes.class, 
                                    academic.lesson_schedules.department_id, academic.lesson_schedules.day, academic.lesson_schedules.from_time, 
                                    academic.lesson_schedules.from_time + academic.lesson_schedules.to_time - 1 AS to_time
                                    FROM academic.lesson_schedule_infos
                                    INNER JOIN academic.lesson_schedules ON academic.lesson_schedules.schedule_id = academic.lesson_schedule_infos.id
                                    INNER JOIN public.employees ON public.employees.id = academic.lesson_schedules.employee_id
                                    INNER JOIN academic.lessons ON academic.lessons.id = academic.lesson_schedules.lesson_id
                                    INNER JOIN academic.classes ON academic.classes.id = academic.lesson_schedules.class_id
                                    WHERE academic.lesson_schedule_infos.id IN (' || p_infos || ')
                                    AND academic.lesson_schedules.day = ' || p_day || '
                                    AND academic.lesson_schedules.employee_id = ' || p_employee || '
                                    AND ' || p_time;
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
        DB::unprepared("DROP FUNCTION IF EXISTS academic.fn_check_schedule_teacher");    
    }
};

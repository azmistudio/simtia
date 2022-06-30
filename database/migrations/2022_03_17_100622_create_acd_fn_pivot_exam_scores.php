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
        $procedure = "CREATE OR REPLACE FUNCTION academic.fn_pivot_exam_scores(
                            IN arr_exam CHARACTER VARYING,
                            IN col_list CHARACTER VARYING
                        )
                        RETURNS CHARACTER VARYING
                        LANGUAGE 'plpgsql'
                        AS $$
                            DECLARE 
                                dynsql VARCHAR;
                            BEGIN
                                dynsql = 'SELECT * FROM crosstab (
                                    ''SELECT b.student_no, INITCAP(b.name) AS student, a.exam_id, a.score::decimal(16,2) FROM academic.exam_scores a
                                      JOIN academic.students b ON b.id = a.student_id WHERE a.exam_id IN ('||arr_exam||') GROUP BY 1,2,3,4 ORDER BY 1,3'',
                                    ''SELECT DISTINCT exam_id FROM academic.exam_scores WHERE exam_id IN ('||arr_exam||') ORDER BY 1''
                                ) AS newtable (student_no VARCHAR, student VARCHAR, '||col_list||');';
                                RETURN dynsql;
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
        DB::unprepared("DROP FUNCTION IF EXISTS academic.fn_pivot_exam_scores");
    }
};

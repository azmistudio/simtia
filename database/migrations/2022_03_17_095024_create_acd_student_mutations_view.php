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
        $view = "CREATE OR REPLACE VIEW academic.student_mutations_view AS (
                    SELECT CONCAT(a.mutation_id, a.department_id, DATE_PART('year', mutation_date)) AS seq, a.mutation_id, a.department_id, DATE_PART('year', mutation_date) AS period, INITCAP(b.name) AS mutation, UPPER(c.name) AS department  
                    FROM academic.student_mutations a
                    JOIN public.references b ON b.id = a.mutation_id
                    JOIN public.departments c ON c.id = a.department_id
                    GROUP BY a.mutation_id, a.department_id, DATE_PART('year', mutation_date), b.name, c.name
                    ORDER BY DATE_PART('year', mutation_date) DESC
                )";
        DB::unprepared($view);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared("DROP VIEW academic.student_mutations_view");
    }
};

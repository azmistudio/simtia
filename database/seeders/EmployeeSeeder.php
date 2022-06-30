<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmployeeSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::table('public.employees')->insertOrIgnore([
            'employee_id' => 101,
            'name' => 'Admin Sistem',
            'title' => '',
            'title_first' => '',
            'title_end' => '',
            'gender' => '1',
            'pob' => 'jakarta',
            'dob' => '1980-01-01',
            'religion' => 1,
            'section' => 46,
            'tribe' => 48,
            'marital' => 2,
            'national_id' => '1234567890',
            'address' => 'Jl. Raya Pondok Indah No 1 Kelurahan Pondok Indah Kecamatan Jakarta Selatan, DKI Jakarta 14123',
            'mobile' => '081233234456',
            'email' => 'admin@simtia.org',
            'work_start' => '2021-01-01',
            'is_active' => 1,
            'is_retired' => 0,
            'logged' => 'system',
            'created_at' => date('Y-m-d H:i:s')
        ]);
        
    }
}

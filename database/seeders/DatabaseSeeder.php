<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        if (DB::table('public.departments')->count() < 1)
        {
            DB::table('public.departments')->insertOrIgnore([
                [ 'name' => 'tahfidz', 'is_active' => 1, 'is_all' => 1, 'logged' => 'system', 'created_at' => date('Y-m-d H:i:s') ],
                [ 'name' => 'ibtidaiyah', 'is_active' => 1, 'is_all' => 0, 'logged' => 'system', 'created_at' => date('Y-m-d H:i:s') ],
                [ 'name' => 'tsanawiyah', 'is_active' => 1, 'is_all' => 0, 'logged' => 'system', 'created_at' => date('Y-m-d H:i:s') ],
                [ 'name' => 'aliyah', 'is_active' => 1, 'is_all' => 0, 'logged' => 'system', 'created_at' => date('Y-m-d H:i:s') ],
            ]);
        }

        if (DB::table('public.institutes')->count() < 1)
        {
            DB::table('public.institutes')->insertOrIgnore([
                [
                    'name' => 'MTA Azmi',
                    'email' => 'email@simtia.org',
                    'website' => 'simtia.org',
                    'address' => 'Jl. Raya Jakarta No. 1 RT 001 RW 013 Kelurahan Jakarta Kecamatan Jakarta, Jakarta Timur - DKI Jakarta 123456',
                    'phone' => '021 1234 5678',
                    'logged' => 'system',
                    'created_at' => date('Y-m-d H:i:s'),
                    'department_id' => 1,
                ],
            ]);
        }

        // accounting
        
        if (DB::table('finance.code_categories')->count() < 1)
        {
            DB::table('finance.code_categories')->insertOrIgnore([
                [ 'category' => 'HARTA', 'order' => 1, 'logged' => 'system', 'created_at' => date('Y-m-d H:i:s'), 'position' => 'D' ],
                [ 'category' => 'HUTANG', 'order' => 2, 'logged' => 'system', 'created_at' => date('Y-m-d H:i:s'), 'position' => 'K' ],
                [ 'category' => 'MODAL', 'order' => 3, 'logged' => 'system', 'created_at' => date('Y-m-d H:i:s'), 'position' => 'K' ],
                [ 'category' => 'PENDAPATAN', 'order' => 4, 'logged' => 'system', 'created_at' => date('Y-m-d H:i:s'), 'position' => 'K' ],
                [ 'category' => 'BIAYA', 'order' => 5, 'logged' => 'system', 'created_at' => date('Y-m-d H:i:s'), 'position' => 'D' ],
            ]);
        }

        if (DB::table('finance.codes')->count() < 1)
        {
            DB::table('finance.codes')->insert([
                [
                    'category_id' => 1, 'code' => '1-100', 
                    'name' => 'Kas dan Bank',
                    'balance' => 0,
                    'remark' => 'Sub Kategori Akun Kas dan Bank',
                    'parent' => 0,
                    'locked' => 1,
                    'logged' => 'system',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'category_id' => 1,
                    'code' => '1-101',
                    'name' => 'Kas Kecil',
                    'balance' => 0,
                    'remark' => 'Petty Cash',
                    'parent' => 1,
                    'locked' => 1,
                    'logged' => 'system',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'category_id' => 1,
                    'code' => '1-102',
                    'name' => 'Kas Bank',
                    'balance' => 0,
                    'remark' => 'Kas yang ada di Bank yang digunakan lembaga',
                    'parent' => 1,
                    'locked' => 0,
                    'logged' => 'system',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'category_id' => 1,
                    'code' => '1-200',
                    'name' => 'Piutang',
                    'balance' => 0,
                    'remark' => 'Sub Kategori Akun Piutang',
                    'parent' => 0,
                    'locked' => 1,
                    'logged' => 'system',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'category_id' => 1,
                    'code' => '1-201',
                    'name' => 'Piutang Santri',
                    'balance' => 0,
                    'remark' => 'Piutang santri kepada lembaga',
                    'parent' => 4,
                    'locked' => 1,
                    'logged' => 'system',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'category_id' => 1,
                    'code' => '1-202',
                    'name' => 'Piutang Pegawai',
                    'balance' => 0,
                    'remark' => 'Piutang Pegawai kepada lembaga',
                    'parent' => 4,
                    'locked' => 1,
                    'logged' => 'system',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'category_id' => 1,
                    'code' => '1-203',
                    'name' => 'Piutang Usaha',
                    'balance' => 0,
                    'remark' => 'Piutang yang lain kepada lembaga',
                    'parent' => 4,
                    'locked' => 0,
                    'logged' => 'system',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'category_id' => 1,
                    'code' => '1-204',
                    'name' => 'Piutang Calon Santri',
                    'balance' => 0,
                    'remark' => 'Piutang Calon Santri kepada lembaga',
                    'parent' => 4,
                    'locked' => 1,
                    'logged' => 'system',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'category_id' => 1,
                    'code' => '1-299',
                    'name' => 'Piutang Lainnya',
                    'balance' => 0,
                    'remark' => 'Piutang Lain-Lain',
                    'parent' => 4,
                    'locked' => 0,
                    'logged' => 'system',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'category_id' => 1,
                    'code' => '1-300',
                    'name' => 'Aset Tetap',
                    'balance' => 0,
                    'remark' => 'Aset tetap milik lembaga',
                    'parent' => 0,
                    'locked' => 1,
                    'logged' => 'system',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'category_id' => 1,
                    'code' => '1-301',
                    'name' => 'Aset Tetap - Tanah',
                    'balance' => 0,
                    'remark' => 'Aset tanah milik lembaga',
                    'parent' => 10,
                    'locked' => 0,
                    'logged' => 'system',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'category_id' => 1,
                    'code' => '1-302',
                    'name' => 'Aset Tetap - Bangunan',
                    'balance' => 0,
                    'remark' => 'Aset bangungan milik lembaga',
                    'parent' => 10,
                    'locked' => 0,
                    'logged' => 'system',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'category_id' => 1,
                    'code' => '1-303',
                    'name' => 'Aset Tetap - Kendaraan',
                    'balance' => 0,
                    'remark' => 'Aset kendaraan milik lembaga',
                    'parent' => 10,
                    'locked' => 0,
                    'logged' => 'system',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'category_id' => 1,
                    'code' => '1-304',
                    'name' => 'Aset Tetap - Perlengkapan Kantor',
                    'balance' => 0,
                    'remark' => 'Aset perlengkapan lembaga',
                    'parent' => 10,
                    'locked' => 1,
                    'logged' => 'system',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'category_id' => 1,
                    'code' => '1-305',
                    'name' => 'Aset Tetap - Peralatan Mengajar',
                    'balance' => 0,
                    'remark' => 'Aset peralatan untuk kegiatan belajar mengajar',
                    'parent' => 10,
                    'locked' => 1,
                    'logged' => 'system',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'category_id' => 1,
                    'code' => '1-306',
                    'name' => 'Aset Tidak Berwujud',
                    'balance' => 0,
                    'remark' => 'Aset tidak berwujud milik lembaga',
                    'parent' => 10,
                    'locked' => 0,
                    'logged' => 'system',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'category_id' => 1,
                    'code' => '1-400',
                    'name' => 'Depresiasi dan Amortisasi',
                    'balance' => 0,
                    'remark' => 'Aset tidak berwujud milik lembaga',
                    'parent' => 0,
                    'locked' => 0,
                    'logged' => 'system',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'category_id' => 1,
                    'code' => '1-401',
                    'name' => 'Akumulasi Penyusutan - Bangunan',
                    'balance' => 0,
                    'remark' => 'Penyusutan bangunan',
                    'parent' => 17,
                    'locked' => 0,
                    'logged' => 'system',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'category_id' => 1,
                    'code' => '1-402',
                    'name' => 'Akumulasi Penyusutan - Kendaraan',
                    'balance' => 0,
                    'remark' => 'Penyusutan kendaraan',
                    'parent' => 17,
                    'locked' => 0,
                    'logged' => 'system',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'category_id' => 1,
                    'code' => '1-403',
                    'name' => 'Akumulasi Penyusutan - Perlengkapan Kantor',
                    'balance' => 0,
                    'remark' => 'Penyusutan perlengkapan kantor',
                    'parent' => 17,
                    'locked' => 1,
                    'logged' => 'system',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'category_id' => 1,
                    'code' => '1-404',
                    'name' => 'Akumulasi Penyusutan - Perlengkapan Mengajar',
                    'balance' => 0,
                    'remark' => 'Penyusutan perlengkapan mengajar',
                    'parent' => 17,
                    'locked' => 1,
                    'logged' => 'system',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'category_id' => 1,
                    'code' => '1-405',
                    'name' => 'Akumulasi Amortisasi',
                    'balance' => 0,
                    'remark' => 'Akumulasi Amortisasi',
                    'parent' => 17,
                    'locked' => 0,
                    'logged' => 'system',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'category_id' => 2,
                    'code' => '2-100',
                    'name' => 'Hutang Lancar',
                    'balance' => 0,
                    'remark' => 'Akun Hutang lembaga',
                    'parent' => 0,
                    'locked' => 1,
                    'logged' => 'system',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'category_id' => 2,
                    'code' => '2-101',
                    'name' => 'Hutang Usaha',
                    'balance' => 0,
                    'remark' => 'Hutang lembaga kepada kreditur',
                    'parent' => 23,
                    'locked' => 1,
                    'logged' => 'system',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'category_id' => 2,
                    'code' => '2-102',
                    'name' => 'Hutang Belum Ditagih',
                    'balance' => 0,
                    'remark' => 'Hutang lembaga yang belum ditagih',
                    'parent' => 23,
                    'locked' => 1,
                    'logged' => 'system',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'category_id' => 2,
                    'code' => '2-103',
                    'name' => 'Hutang Gaji',
                    'balance' => 0,
                    'remark' => 'Hutang gaji pegawai yang belum dibayar',
                    'parent' => 23,
                    'locked' => 1,
                    'logged' => 'system',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'category_id' => 2,
                    'code' => '2-199',
                    'name' => 'Hutang Lain - Lain',
                    'balance' => 0,
                    'remark' => 'Hutang lainnya',
                    'parent' => 23,
                    'locked' => 0,
                    'logged' => 'system',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'category_id' => 2,
                    'code' => '2-200',
                    'name' => 'Hutang Tidak Lancar',
                    'balance' => 0,
                    'remark' => 'Hutang tidak lancar',
                    'parent' => 0,
                    'locked' => 0,
                    'logged' => 'system',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'category_id' => 2,
                    'code' => '2-300',
                    'name' => 'Kewajiban Jangka Panjang',
                    'balance' => 0,
                    'remark' => 'Hutang Jangka Panjang',
                    'parent' => 0,
                    'locked' => 0,
                    'logged' => 'system',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'category_id' => 3,
                    'code' => '3-100',
                    'name' => 'Ekuitas',
                    'balance' => 0,
                    'remark' => 'Akun Ekuitas',
                    'parent' => 0,
                    'locked' => 1,
                    'logged' => 'system',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'category_id' => 3,
                    'code' => '3-101',
                    'name' => 'Modal Saham',
                    'balance' => 0,
                    'remark' => 'Modal yang ditanamkan oleh penanam modal kepada lembaga',
                    'parent' => 30,
                    'locked' => 1,
                    'logged' => 'system',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'category_id' => 3,
                    'code' => '3-102',
                    'name' => 'Tambahan Modal Disetor',
                    'balance' => 0,
                    'remark' => 'Tambahan Modal yang disetorkan kepada lembaga',
                    'parent' => 30,
                    'locked' => 1,
                    'logged' => 'system',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'category_id' => 3,
                    'code' => '3-103',
                    'name' => 'Laba Ditahan',
                    'balance' => 0,
                    'remark' => 'Laba hasil usaha yang belum dibayarkan kepada investor',
                    'parent' => 30,
                    'locked' => 1,
                    'logged' => 'system',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'category_id' => 3,
                    'code' => '3-199',
                    'name' => 'Ekuitas Saldo Awal',
                    'balance' => 0,
                    'remark' => 'Penyeimbang saldo awal akun',
                    'parent' => 30,
                    'locked' => 1,
                    'logged' => 'system',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'category_id' => 4,
                    'code' => '4-100',
                    'name' => 'Akun Pendapatan',
                    'balance' => 0,
                    'remark' => 'Transaksi Pendapatan lembaga',
                    'parent' => 0,
                    'locked' => 1,
                    'logged' => 'system',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'category_id' => 4,
                    'code' => '4-101',
                    'name' => 'Pendapatan SPP',
                    'balance' => 0,
                    'remark' => 'Pendapatan dari pembayaran SPP santri',
                    'parent' => 35,
                    'locked' => 1,
                    'logged' => 'system',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'category_id' => 4,
                    'code' => '4-102',
                    'name' => 'Pendapatan DSP',
                    'balance' => 0,
                    'remark' => 'Pendapatan dari pembayaran DSP santri',
                    'parent' => 35,
                    'locked' => 1,
                    'logged' => 'system',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'category_id' => 4,
                    'code' => '4-103',
                    'name' => 'Pendapatan Sukarela Santri',
                    'balance' => 0,
                    'remark' => 'Pendapatan dari perolehan dana sukarela santri',
                    'parent' => 35,
                    'locked' => 1,
                    'logged' => 'system',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'category_id' => 4,
                    'code' => '4-104',
                    'name' => 'Pendapatan Sukarela Calon Santri',
                    'balance' => 0,
                    'remark' => 'Pendapatan dari perolehan dana sukarela calon santri',
                    'parent' => 35,
                    'locked' => 1,
                    'logged' => 'system',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'category_id' => 4,
                    'code' => '4-105',
                    'name' => 'Pendapatan Hibah',
                    'balance' => 0,
                    'remark' => 'Pendapatan dari perolehan dana hibah',
                    'parent' => 35,
                    'locked' => 0,
                    'logged' => 'system',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'category_id' => 4,
                    'code' => '4-106',
                    'name' => 'Diskon SPP',
                    'balance' => 0,
                    'remark' => '',
                    'parent' => 35,
                    'locked' => 1,
                    'logged' => 'system',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'category_id' => 4,
                    'code' => '4-107',
                    'name' => 'Diskon DSP',
                    'balance' => 0,
                    'remark' => '',
                    'parent' => 35,
                    'locked' => 1,
                    'logged' => 'system',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'category_id' => 4,
                    'code' => '4-108',
                    'name' => 'Diskon Sukarela Santri',
                    'balance' => 0,
                    'remark' => '',
                    'parent' => 35,
                    'locked' => 1,
                    'logged' => 'system',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'category_id' => 4,
                    'code' => '4-109',
                    'name' => 'Diskon Sukarela Calon Santri',
                    'balance' => 0,
                    'remark' => '',
                    'parent' => 35,
                    'locked' => 1,
                    'logged' => 'system',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'category_id' => 4,
                    'code' => '4-199',
                    'name' => 'Diskon Lainnya',
                    'balance' => 0,
                    'remark' => '',
                    'parent' => 35,
                    'locked' => 0,
                    'logged' => 'system',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'category_id' => 5,
                    'code' => '5-100',
                    'name' => 'Beban',
                    'balance' => 0,
                    'remark' => 'Beban yang dikeluarkan oleh lembaga',
                    'parent' => 0,
                    'locked' => 1,
                    'logged' => 'system',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'category_id' => 5,
                    'code' => '5-101',
                    'name' => 'Beban Transportasi',
                    'balance' => 0,
                    'remark' => 'Beban yang dikeluarkan untuk pembiayaan transportasi',
                    'parent' => 46,
                    'locked' => 1,
                    'logged' => 'system',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'category_id' => 5,
                    'code' => '5-102',
                    'name' => 'Beban Listrik',
                    'balance' => 0,
                    'remark' => 'Beban yang dikeluarkan untuk melunasi tagihan PLN',
                    'parent' => 46,
                    'locked' => 1,
                    'logged' => 'system',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'category_id' => 5,
                    'code' => '5-103',
                    'name' => 'Beban Komunikasi',
                    'balance' => 0,
                    'remark' => 'Beban yang dikeluarkan untuk pembiayaan tagihan telpon/pulsa handphone',
                    'parent' => 46,
                    'locked' => 0,
                    'logged' => 'system',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'category_id' => 5,
                    'code' => '5-104',
                    'name' => 'Beban Internet',
                    'balance' => 0,
                    'remark' => 'Beban yang dikeluarkan untuk pembiayaan tagihan Internet',
                    'parent' => 46,
                    'locked' => 0,
                    'logged' => 'system',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'category_id' => 5,
                    'code' => '5-105',
                    'name' => 'Beban ATK',
                    'balance' => 0,
                    'remark' => 'Beban yang dikeluarkan untuk pembelian rutin ATK',
                    'parent' => 46,
                    'locked' => 0,
                    'logged' => 'system',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'category_id' => 5,
                    'code' => '5-106',
                    'name' => 'Beban Gaji',
                    'balance' => 0,
                    'remark' => 'Beban Gaji pegawai dan tenaga pengajar',
                    'parent' => 46,
                    'locked' => 0,
                    'logged' => 'system',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'category_id' => 5,
                    'code' => '5-107',
                    'name' => 'Beban Iklan dan Promosi',
                    'balance' => 0,
                    'remark' => 'Beban yang dikeluarkan untuk pembiayaan iklan dan promosi lembaga',
                    'parent' => 46,
                    'locked' => 0,
                    'logged' => 'system',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'category_id' => 5,
                    'code' => '5-108',
                    'name' => 'Beban Biaya Sewa - Bangunan',
                    'balance' => 0,
                    'remark' => 'Beban biaya sewa Bangunan',
                    'parent' => 46,
                    'locked' => 0,
                    'logged' => 'system',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'category_id' => 5,
                    'code' => '5-109',
                    'name' => 'Beban Biaya Sewa - Kendaraan',
                    'balance' => 0,
                    'remark' => 'Beban biaya sewa Kendaraan',
                    'parent' => 46,
                    'locked' => 0,
                    'logged' => 'system',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'category_id' => 5,
                    'code' => '5-110',
                    'name' => 'Beban Biaya Sewa - Lain - Lain',
                    'balance' => 0,
                    'remark' => 'Beban biaya sewa Lain - Lain',
                    'parent' => 46,
                    'locked' => 0,
                    'logged' => 'system',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'category_id' => 5,
                    'code' => '5-199',
                    'name' => 'Beban Lainnya',
                    'balance' => 0,
                    'remark' => 'Beban biaya Lain - Lain',
                    'parent' => 46,
                    'locked' => 0,
                    'logged' => 'system',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'category_id' => 5,
                    'code' => '5-200',
                    'name' => 'Penyusutan',
                    'balance' => 0,
                    'remark' => 'Biaya penyusutan aset lembaga',
                    'parent' => 0,
                    'locked' => 1,
                    'logged' => 'system',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'category_id' => 5,
                    'code' => '5-201',
                    'name' => 'Penyusutan Bangunan',
                    'balance' => 0,
                    'remark' => 'Biaya penyusutan bangunan',
                    'parent' => 58,
                    'locked' => 0,
                    'logged' => 'system',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'category_id' => 5,
                    'code' => '5-202',
                    'name' => 'Penyusutan Kendaraan',
                    'balance' => 0,
                    'remark' => 'Biaya penyusutan kendaraan',
                    'parent' => 58,
                    'locked' => 0,
                    'logged' => 'system',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'category_id' => 5,
                    'code' => '5-203',
                    'name' => 'Penyusutan Perlengkapan Kantor',
                    'balance' => 0,
                    'remark' => 'Biaya penyusutan perlengkapan kantor',
                    'parent' => 58,
                    'locked' => 0,
                    'logged' => 'system',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'category_id' => 5,
                    'code' => '5-204',
                    'name' => 'Penyusutan Perlengkapan Mengajar',
                    'balance' => 0,
                    'remark' => 'Biaya penyusutan perlengkapan mengajar',
                    'parent' => 58,
                    'locked' => 0,
                    'logged' => 'system',
                    'created_at' => date('Y-m-d H:i:s')
                ],
            ]);
        }

        if (DB::table('finance.receipt_categories')->count() < 1)
        {
            DB::table('finance.receipt_categories')->insert([
                [
                    'code' => 'JTT',
                    'category' => 'Iuran Wajib Santri',
                    'order' => 1,
                    'student' => 1,
                    'logged' => 'system',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'code' => 'SKR',
                    'category' => 'Iuran Sukarela Santri',
                    'order' => 2,
                    'student' => 1,
                    'logged' => 'system',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'code' => 'CSWJB',
                    'category' => 'Iuran Wajib Calon Santri',
                    'order' => 3,
                    'student' => 1,
                    'logged' => 'system',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'code' => 'CSSKR',
                    'category' => 'Iuran Sukarela Calon Santri',
                    'order' => 4,
                    'student' => 1,
                    'logged' => 'system',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'code' => 'LNN',
                    'category' => 'Penerimaan Lainnya',
                    'order' => 5,
                    'student' => 1,
                    'logged' => 'system',
                    'created_at' => date('Y-m-d H:i:s')
                ],
            ]);
        }

        if (DB::table('public.quran_surahs')->count() < 1)
        {
            DB::table('public.quran_surahs')->insert([
                [ 'surah' => 'Al Fatihah','total' => 7, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'surah' => 'Al Baqarah', 'total' => 286, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'surah' => 'Ali Imran', 'total' => 200, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'surah' => 'An Nisa', 'total' => 176, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'surah' => 'Al Maidah', 'total' => 120, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'surah' => 'Al An`am', 'total' => 165, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'surah' => 'Al A`raf', 'total' => 206, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'surah' => 'Al Anfal', 'total' => 75, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'surah' => 'At Taubah', 'total' => 129, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'surah' => 'Yunus', 'total' => 109, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'surah' => 'Hud', 'total' => 123, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'surah' => 'Yusuf', 'total' => 111, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'surah' => 'Ar Ra`d', 'total' => 43, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'surah' => 'Ibrahim', 'total' => 52, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'surah' => 'Al Hijr', 'total' => 99, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'surah' => 'An Nahl', 'total' => 128, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'surah' => 'Al Isra', 'total' => 111, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'surah' => 'Al Kahf', 'total' => 110, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'surah' => 'Mariam', 'total' => 98, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'surah' => 'Taha', 'total' => 135, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'surah' => 'Al Anbiya', 'total' => 112, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'surah' => 'Al Hajj', 'total' => 78, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'surah' => 'Al Mu`minun', 'total' => 118, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'surah' => 'An Nur', 'total' => 64, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'surah' => 'Al Furqan', 'total' => 77, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'surah' => 'Asy Syu`ara', 'total' => 227, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'surah' => 'An Naml', 'total' => 93, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'surah' => 'Al Qashas', 'total' => 88, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'surah' => 'Al `Ankabut', 'total' => 69, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'surah' => 'Ar Rum', 'total' => 60, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'surah' => 'Lukman', 'total' => 34, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'surah' => 'As Sajdah', 'total' => 30, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'surah' => 'Al Ahzab', 'total' => 73, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'surah' => 'Saba', 'total' => 54, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'surah' => 'Fatir', 'total' => 45, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'surah' => 'Yasin', 'total' => 83, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'surah' => 'As Saffat', 'total' => 182, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'surah' => 'Sad', 'total' => 88, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'surah' => 'Az Zumar', 'total' => 75, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'surah' => 'Gafir', 'total' => 85, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'surah' => 'Fussilat', 'total' => 54, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'surah' => 'Asy Syura', 'total' => 53, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'surah' => 'Az Zukhruf', 'total' => 89, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'surah' => 'Ad Dukhan', 'total' => 59, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'surah' => 'Al Jasiyah', 'total' => 37, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'surah' => 'Al Ahqaf', 'total' => 35, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'surah' => 'Muhammad', 'total' => 38, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'surah' => 'Al Fath', 'total' => 29, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'surah' => 'Al Hujurat', 'total' => 18, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'surah' => 'Qaf', 'total' => 45, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'surah' => 'Az Zariyat', 'total' => 60, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'surah' => 'At Tur', 'total' => 49, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'surah' => 'An Najm', 'total' => 62, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'surah' => 'Al Qamar', 'total' => 55, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'surah' => 'Ar Rahman', 'total' => 78, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'surah' => 'Al Waqi`ah', 'total' => 96, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'surah' => 'Al Hadid', 'total' => 29, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'surah' => 'Al Mujadalah', 'total' => 22, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'surah' => 'Al Hasyr', 'total' => 24, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'surah' => 'Al Mumtahanah', 'total' => 13, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'surah' => 'As Saff', 'total' => 14, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'surah' => 'Al Jumu`ah', 'total' => 11, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'surah' => 'Al Munafiqun', 'total' => 11, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'surah' => 'At Tagabun', 'total' => 18, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'surah' => 'At Talaq', 'total' => 12, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'surah' => 'At Tahrim', 'total' => 12, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'surah' => 'Al Mulk', 'total' => 30, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'surah' => 'Al Qalam', 'total' => 52, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'surah' => 'Al Haqqah', 'total' => 52, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'surah' => 'Al Ma`arij', 'total' => 44, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'surah' => 'Nuh', 'total' => 28, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'surah' => 'Al Jinn', 'total' => 28, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'surah' => 'Al Muzzammil', 'total' => 20, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'surah' => 'Al Muddassir', 'total' => 56, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'surah' => 'Al Qiyamah', 'total' => 40, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'surah' => 'Al Insan', 'total' => 31, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'surah' => 'Al Mursalat', 'total' => 50, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'surah' => 'An Naba`', 'total' => 40, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'surah' => 'An Nazi`at', 'total' => 46, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'surah' => '`Abasa', 'total' => 42, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'surah' => 'At Takwir', 'total' => 29, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'surah' => 'Al Infitar', 'total' => 19, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'surah' => 'Al Mutaffifin', 'total' => 36, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'surah' => 'Al Insyiqaq', 'total' => 25, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'surah' => 'Al Buruj', 'total' => 22, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'surah' => 'At Tariq', 'total' => 17, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'surah' => 'Al A`la', 'total' => 19, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'surah' => 'Al Gasyiyah', 'total' => 26, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'surah' => 'Al Fajr', 'total' => 30, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'surah' => 'Al Balad', 'total' => 20, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'surah' => 'Asy Syams', 'total' => 15, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'surah' => 'Al Lail', 'total' => 21, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'surah' => 'Ad Duha', 'total' => 11, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'surah' => 'Asy Syarh', 'total' => 8, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'surah' => 'At Tin', 'total' => 8, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'surah' => 'Al `Alaq', 'total' => 19, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'surah' => 'Al Qadr', 'total' => 5, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'surah' => 'Al Bayyinah', 'total' => 8, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'surah' => 'Az Zalzalah', 'total' => 8, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'surah' => 'Al `Adiyat', 'total' => 11, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'surah' => 'Al Qar`ah', 'total' => 11, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'surah' => 'At Takasur', 'total' => 8, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'surah' => 'Al `Asr', 'total' => 3, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'surah' => 'Al Humazah', 'total' => 9, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'surah' => 'Al Fil', 'total' => 5, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'surah' => 'Quraisy', 'total' => 4, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'surah' => 'Al Ma`un', 'total' => 7, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'surah' => 'Al Kausar', 'total' => 3, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'surah' => 'Al Kafirun', 'total' => 6, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'surah' => 'An Nasr', 'total' => 3, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'surah' => 'Al Lahab', 'total' => 5, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'surah' => 'Al Ikhlas', 'total' => 4, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'surah' => 'Al Falaq', 'total' => 5, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'surah' => 'An Nas', 'total' => 6, 'created_at' => date('Y-m-d H:i:s') ],
            ]);
        }

        if (DB::table('public.quran_juzs')->count() < 1)
        {
            DB::table('public.quran_juzs')->insert([
                [ 'total' => 148, 'from_surah' => 1, 'from_ayah' => 1, 'to_surah' => 2, 'to_ayah' => 141, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'total' => 111, 'from_surah' => 2, 'from_ayah' => 142, 'to_surah' => 2, 'to_ayah' => 252, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'total' => 126, 'from_surah' => 2, 'from_ayah' => 253, 'to_surah' => 3, 'to_ayah' => 91, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'total' => 131, 'from_surah' => 3, 'from_ayah' => 92, 'to_surah' => 4, 'to_ayah' => 23, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'total' => 124, 'from_surah' => 4, 'from_ayah' => 24, 'to_surah' => 4, 'to_ayah' => 147, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'total' => 110, 'from_surah' => 4, 'from_ayah' => 148, 'to_surah' => 5, 'to_ayah' => 82, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'total' => 149, 'from_surah' => 5, 'from_ayah' => 83, 'to_surah' => 6, 'to_ayah' => 110, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'total' => 142, 'from_surah' => 6, 'from_ayah' => 111, 'to_surah' => 7, 'to_ayah' => 87, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'total' => 159, 'from_surah' => 7, 'from_ayah' => 88, 'to_surah' => 8, 'to_ayah' => 40, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'total' => 127, 'from_surah' => 8, 'from_ayah' => 41, 'to_surah' => 9, 'to_ayah' => 93, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'total' => 151, 'from_surah' => 9, 'from_ayah' => 94, 'to_surah' => 11, 'to_ayah' => 5, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'total' => 170, 'from_surah' => 11, 'from_ayah' => 6, 'to_surah' => 12, 'to_ayah' => 52, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'total' => 154, 'from_surah' => 12, 'from_ayah' => 53, 'to_surah' => 14, 'to_ayah' => 52, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'total' => 227, 'from_surah' => 15, 'from_ayah' => 1, 'to_surah' => 16, 'to_ayah' => 128, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'total' => 185, 'from_surah' => 17, 'from_ayah' => 1, 'to_surah' => 18, 'to_ayah' => 74, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'total' => 269, 'from_surah' => 18, 'from_ayah' => 75, 'to_surah' => 20, 'to_ayah' => 135, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'total' => 190, 'from_surah' => 21, 'from_ayah' => 1, 'to_surah' => 22, 'to_ayah' => 78, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'total' => 202, 'from_surah' => 23, 'from_ayah' => 1, 'to_surah' => 25, 'to_ayah' => 20, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'total' => 339, 'from_surah' => 25, 'from_ayah' => 21, 'to_surah' => 27, 'to_ayah' => 55, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'total' => 171, 'from_surah' => 27, 'from_ayah' => 56, 'to_surah' => 29, 'to_ayah' => 45, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'total' => 178, 'from_surah' => 29, 'from_ayah' => 46, 'to_surah' => 33, 'to_ayah' => 30, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'total' => 169, 'from_surah' => 33, 'from_ayah' => 31, 'to_surah' => 36, 'to_ayah' => 27, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'total' => 357, 'from_surah' => 36, 'from_ayah' => 28, 'to_surah' => 39, 'to_ayah' => 31, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'total' => 175, 'from_surah' => 39, 'from_ayah' => 32, 'to_surah' => 41, 'to_ayah' => 46, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'total' => 246, 'from_surah' => 41, 'from_ayah' => 47, 'to_surah' => 45, 'to_ayah' => 32, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'total' => 195, 'from_surah' => 45, 'from_ayah' => 33, 'to_surah' => 51, 'to_ayah' => 30, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'total' => 399, 'from_surah' => 51, 'from_ayah' => 31, 'to_surah' => 57, 'to_ayah' => 29, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'total' => 137, 'from_surah' => 58, 'from_ayah' => 1, 'to_surah' => 66, 'to_ayah' => 12, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'total' => 431, 'from_surah' => 67, 'from_ayah' => 1, 'to_surah' => 77, 'to_ayah' => 50, 'created_at' => date('Y-m-d H:i:s') ],
                [ 'total' => 564, 'from_surah' => 78, 'from_ayah' => 1, 'to_surah' => 114, 'to_ayah' => 6, 'created_at' => date('Y-m-d H:i:s') ],
            ]);
        }

        if (DB::table('public.configs')->count() < 1)
        {
            DB::table('public.configs')->insert([
                [ 
                    'slug' => 'app_version', 
                    'key' => '1.0', 
                    'value' => '##### Bugs
##### Perbaikan 
##### Pembaruan' 
                ],
            ]);
        }
    }
}

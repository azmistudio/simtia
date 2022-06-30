<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Modules\HR\Entities\Employee;

class ResetData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:reset {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset data in database schema';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $object = $this->argument('name');
        $email = $this->ask('Silahkan input email administrator:');
        $password = $this->ask('Silahkan input kata sandi administrator:');
        if (!empty($email) && !empty($password))
        {
            $credentials = [ 'email' => $email, 'password' => $password ];
            if (Auth::attempt($credentials))
            {
                // get employee status
                $employee = Employee::where('email', $email)->first();
                if ($employee->is_active == 0)
                {
                    $this->info("Pengguna sudah tidak aktif.");
                } else {
                    // get user role
                    $role = Auth::user()->roles->pluck('name');
                    if ($role[0] != 'Administrator')
                    {
                        $this->info("Pengguna tidak memiliki hak akses administrator.");
                    } else {
                        if ($this->confirm('Anda akan melakukan hapus data di database, tetap lanjutkan?')) {
                            switch ($object)
                            {
                                case 'all':
                                    $this->call('down');
                                    $this->call('db:backup', ['name' => $object]);
                                    $this->resetSchema('finance');
                                    $this->resetSchema('academic');
                                    $this->resetSchema('public');
                                    $this->call('up');
                                    $this->info("Data semua skema berhasil di hapus.");
                                    break;
                                case 'academic':
                                    $this->call('down');
                                    $this->call('db:backup', ['name' => $object]);
                                    $this->resetSchema('finance');
                                    $this->resetSchema($object);
                                    $this->call('up');
                                    $this->info("Data {$object} berhasil di hapus.");
                                    break;
                                case 'finance':
                                    $this->call('down');
                                    $this->call('db:backup', ['name' => $object]);
                                    $this->resetSchema($object);
                                    $this->call('up');
                                    $this->info("Data {$object} berhasil dihapus.");
                                    break;
                                default:
                                    $this->info("Skema {$object} tidak ditemukan.");
                                    break;
                            }
                        } else {
                            $this->info("Hapus data {$object} dibatalkan.");
                        }
                    }
                }
            } else {
                $this->info("Kombinasi email dan kata sandi tidak ditemukan.");
            }
        } else {
            $this->info("Email dan kata sandi administrator wajib diisi.");
        }
    }

    private function resetSchema($schema)
    {
        $procedure = config('database.reset.'.$schema);
        return DB::unprepared($procedure);
    }
}

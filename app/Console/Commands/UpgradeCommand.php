<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Config;
use Modules\HR\Entities\Employee;
use Parsedown;
use Exception;

class UpgradeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:upgrade {email} {password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Upgrade application';

    private $tmp_backup_dir = null;

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $email = $this->argument('email');
        $password = $this->argument('password');
        if (Auth::attempt(['email' => $email, 'password' => $password]))
        {
            // get employee status
            $employee = Employee::where('email', $email)->first();
            if ($employee->is_active == 0)
            {
                $this->error('Pengguna sudah tidak aktif.');
            } else {
                // get user role
                $role = Auth::user()->roles->pluck('name');
                if ($role[0] != 'Administrator')
                {
                    $this->error('[ERROR] Pengguna tidak memiliki hak akses administrator.');
                } else {
                    $this->update();
                }
            }
        } else {
            $this->error('[ERROR] Kata sandi salah.');
        }
    }

    // Download and install update
    private function update()
    {
        set_time_limit(0);
        $this->tmp_backup_dir = config('app.updater_path') .'/backup_'.date('Ymd');
        $lastVersionInfo = $this->getLastVersion();
        if ( $lastVersionInfo['version'] <= $this->getCurrentVersion() )
        {
            $this->warn('[WARN] Aplikasi sudah terpasang versi terbaru.');
            exit;
        } 

        sleep(5);
        try 
        {
            $update_path = null;
            if (($update_path = $this->download($lastVersionInfo['archive'])) === false)
            {
                throw new Exception('Terjadi masalah saat mengunduh', 1);
            }

            $this->call('down');
            sleep(5);

            $status = $this->install($lastVersionInfo['version'], $update_path, $lastVersionInfo['archive']);
            if ($status)
            {
                DB::table('public.configs')->insert([
                    'slug'  => 'app_version',
                    'key'   => $lastVersionInfo['version'],
                    'value' => $this->getUpdateDesc()
                ]);
                $this->call('up'); //restore system UP status
                $this->info('[FINISH] Aplikasi berhasil diperbarui.');
            } else {
                throw new Exception('Terjadi masalah saat proses install berkas terbaru.');
            }
        } catch (Exception $e) {
            $this->error('[FAILED] Terjadi masalah, ' . $e->getMessage());
        }
    }

    // Extract and install update
    private function install($lastVersion, $update_path, $archive)
    {
        try
        {
            $zipHandle = new \ZipArchive();
            if ($zipHandle->open($update_path, \ZipArchive::RDONLY) !== true) 
            {
                throw new Exception('Terjadi kesalahan saat mengekstrak berkas');
            }
            $archive = substr($archive,0, -4);
            $zipHandle->extractTo(config('app.updater_path'));
            $zipHandle->close();

            // copy folder
            File::copyDirectory(config('app.updater_path').'/'.$archive, base_path());

            // call seeder
            $this->call('db:seed', ['--class' => 'DatabaseSeeder', '--force' => true]);
            $this->call('db:seed', ['--class' => 'ReferenceSeeder', '--force' => true]);
            $this->call('db:seed', ['--class' => 'PermissionSeeder', '--force' => true]);

            // update .env and config file
            $this->setEnvValue('APP_VERSION', 'app.version', $lastVersion);

            File::delete($update_path); //clean TMP
        } catch (Exception $e) { 
            return false; 
        }
        return true;
    }

    /*
    * Download Update from $updater_url to $tmp_path (local folder).
    */
    private function download($update_name)
    {
        try
        {
            $filename_tmp = config('app.updater_path').'/'.$update_name;
            if ( !is_file( $filename_tmp ) ) 
            {
                $newUpdate = file_get_contents(config('app.updater_url').'/'.$update_name);
                $dlHandler = fopen($filename_tmp, 'w');
                if ( !fwrite($dlHandler, $newUpdate) )
                {
                    throw new Exception('Tidak dapat menyimpan berkas terbaru', 1);
                    exit();
                }
            }
        } catch (Exception $e) { 
            return false; 
        }
        return $filename_tmp;
    }

    /*
    * Return current version (as plain text).
    */
    public function getCurrentVersion()
    {
        return config('app.version');
    }

    // Get last version
    private function getLastVersion()
    {
        $content = file_get_contents(config('app.updater_url').'/meta.json');
        return json_decode($content, true);
    }

    // Get update description
    private function getUpdateDesc()
    {
        return file_get_contents(config('app.updater_url').'/description.md');
    }

    // Set .env value
    private function setEnvValue($envName, $configKey, $newVal)
    {
        file_put_contents(App::environmentFilePath(), str_replace(
            $envName . '=' . config($configKey), 
            $envName . '=' . $newVal, 
            file_get_contents(App::environmentFilePath())
        ));
        Config::set($configKey, $newVal);
        // Reload cached config
        if (file_exists(App::getCachedConfigPath()))
        {
            $this->call('config:cache');
        }
    }
}

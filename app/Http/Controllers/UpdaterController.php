<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;
use App\Http\Traits\HelperTrait;
use Artisan;
use Parsedown;
use Exception;

class UpdaterController extends Controller
{

    use HelperTrait;
    
    public function check()
    {
        $lastVersionInfo = $this->getLastVersion();
        if ( version_compare($lastVersionInfo['version'], config('app.version'), ">") )
        {
            return $lastVersionInfo['version'];
        }
        return '';
    }

    public function description()
    {
        $lastVersionInfo = $this->getLastVersion();
        $Parsedown = new Parsedown();
        $content = file_get_contents(config('app.updater_url').'/description.md');
        $html = '<h6>&raquo; Versi aplikasi saat ini: <b>' . config('app.version') . '</b></h6>';
        $html .= '<h6>&raquo; Versi terbaru: <b>' . $lastVersionInfo['version'] . '</b></h6>';
        $html .= '<b>Log perubahan versi terbaru:</b><br/><hr/>';
        $html .= $Parsedown->text($content);
        return $html;
    }

    public function updateDownload(Request $request)
    {
        set_time_limit(0);
        try
        {
            $lastVersionInfo = $this->getLastVersion();
            if ( $lastVersionInfo['version'] <= $this->getCurrentVersion() )
            {
                throw new Exception('Aplikasi sudah terpasang versi terbaru.', 1);
            } 

            if (!file_exists(config('app.updater_path')))
            {
                File::makeDirectory(config('app.updater_path'));
            }

            $filename_tmp = config('app.updater_path').'/'.$lastVersionInfo['archive'];
            if ( !is_file( $filename_tmp ) ) 
            {
                $newUpdate = file_get_contents(config('app.updater_url').'/'.$lastVersionInfo['archive']);
                $dlHandler = fopen($filename_tmp, 'w');
                if ( !fwrite($dlHandler, $newUpdate) )
                {
                    throw new Exception('Tidak dapat menyimpan berkas terbaru', 1);
                }
            }
            $payload = array(
                'filename_tmp' => $filename_tmp,
                'archive' => $lastVersionInfo['archive'],
                'lastVersion' => $lastVersionInfo['version']
            );
            $response = $this->getResponse('info', $payload);
        } catch (Exception $e) { 
            $response = $this->getResponse('error', $e->getMessage());
        }
        return response()->json($response);
    }

    public function updateExtract(Request $request)
    {
        set_time_limit(0);
        try
        {
            $zipHandle = new \ZipArchive();
            if ($zipHandle->open($request->filename_tmp, \ZipArchive::RDONLY) !== true) 
            {
                throw new Exception('Terjadi kesalahan saat mengekstrak berkas');
            }
            $archive = substr($request->archive,0, -4);
            $zipHandle->extractTo(config('app.updater_path'));
            $zipHandle->close();
            $payload = array(
                'filename_tmp' => $request->filename_tmp,
                'archive' => $request->archive,
                'lastVersion' => $request->lastVersion
            );
            $response = $this->getResponse('info', $payload);
        } catch (Exception $e) { 
            $response = $this->getResponse('error', $e->getMessage());
        }
        return response()->json($response);
    }

    public function updateInstall(Request $request)
    {
        set_time_limit(0);
        try
        {
            Artisan::call('down');

            // copy folder
            $archive = substr($request->archive,0, -4);
            File::copyDirectory(config('app.updater_path').'/'.$archive, base_path());

            // call migration
            Artisan::call('migrate', ['--force' => true]);

            // call seeder
            Artisan::call('db:seed', ['--class' => 'DatabaseSeeder', '--force' => true]);
            Artisan::call('db:seed', ['--class' => 'ReferenceSeeder', '--force' => true]);
            Artisan::call('db:seed', ['--class' => 'PermissionSeeder', '--force' => true]);

            // update .env and config file
            $this->setEnvValue('APP_VERSION', 'app.version', $request->lastVersion);

            // delete archive
            File::delete($request->filename_tmp);

            DB::table('public.configs')->insert([
                'slug'  => 'app_version',
                'key'   => $request->lastVersion,
                'value' => $this->getUpdateDesc()
            ]);

            Artisan::call('up');

            $response = $this->getResponse('info', 'Aplikasi SIMTIA berhasil diperbarui');
        } catch (Exception $e) { 
            $response = $this->getResponse('error', $e->getMessage());
        }
        return response()->json($response);
    }


    private function getCurrentVersion()
    {
        return config('app.version');
    }

    private function getLastVersion()
    {
        try 
        {
            $response = Http::get(config('app.updater_url').'/meta.json');
            return json_decode($response->body(), true);
        } catch (Exception $e) {
            return $this->getResponse('error', $e->getMessage());
        }
    }

    private function getUpdateDesc()
    {
        try 
        {
            $response = Http::get(config('app.updater_url').'/description.md');
            return $response->body();
        } catch (Exception $e) {
            return $this->getResponse('error', $e->getMessage());
        }
    }

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

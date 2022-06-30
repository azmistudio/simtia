<?php
/*
* @author: Pietro Cinaglia
* 	.website: http://linkedin.com/in/pietrocinaglia
*/
namespace pcinaglia\laraUpdater;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Config;
use Artisan;
use Auth;
use Parsedown;

class LaraUpdaterController extends Controller
{

    private $tmp_backup_dir = null;

    private function checkPermission(){

        if ( config('laraupdater.allow_users_id') !== null )
        {
            // 1
            if ( config('laraupdater.allow_users_id') === false ) return true;
            // 2
            if ( in_array(auth()->user()->id, config('laraupdater.allow_users_id')) === true ) return true;
        }
        return false;
    }
    /*
    * Download and Install Update.
    */
    public function update()
    {
        set_time_limit(0);
        echo "<h2>".trans("laraupdater.LaraUpdater")."</h2>";
        echo '<h4><a href="'.url('/').'">'.trans("laraupdater.Return_to_App_HOME").'</a></h4>';

        if ( ! $this->checkPermission() )
        {
            echo trans("laraupdater.ACTION_NOT_ALLOWED.");
            exit;
        }

        $lastVersionInfo = $this->getLastVersion();

        if ( $lastVersionInfo['version'] <= $this->getCurrentVersion() )
        {
            echo '<p>&raquo; '.trans("laraupdater.Your_System_IS_ALREADY_UPDATED_to_last version").' !</p>';
            exit;
        }
        
        sleep(5);
        try
        {
            $this->tmp_backup_dir = base_path().'/tmp/backup_'.date('Ymd');

            echo '<p>'.trans("laraupdater.UPDATE_FOUND").': '.$lastVersionInfo['version'].' <i>('.trans("laraupdater.current_version").': '.$this->getCurrentVersion().')</i></p>';
            echo '<p>&raquo; '.trans("laraupdater.Update_downloading_..").' ';
            $update_path = null;
            if ( ($update_path = $this->download($lastVersionInfo['archive'])) === false)
                throw new \Exception(trans("laraupdater.Error_during_download."));

            echo trans("laraupdater.OK").' </p>';

            Artisan::call('down');
            echo '<p>&raquo; '.trans("laraupdater.SYSTEM_Mantence_Mode").' => '.trans("laraupdater.ON").'</p>';
            sleep(5);
            $status = $this->install($lastVersionInfo['version'], $update_path, $lastVersionInfo['archive']);

            if ($status)
            {
                DB::table('public.configs')->insert([
                    'slug'  => 'app_version',
                    'key'   => $lastVersionInfo['version'],
                    'value' => $this->getUpdateDesc()
                ]);
                Artisan::call('up'); //restore system UP status
                echo '<p>&raquo; '.trans("laraupdater.SYSTEM_Mantence_Mode").' => '.trans("laraupdater.OFF").'</p>';
                echo '<p class="success">'.trans("laraupdater.SYSTEM_IS_NOW_UPDATED_TO_VERSION").': '.$lastVersionInfo['version'].'</p>';
            } else {
                throw new \Exception(trans("laraupdater.Error_during_download."));
            }

        } catch (\Exception $e) {
            echo '<p>'.trans("laraupdater.ERROR_DURING_UPDATE_(!!check_the_update_archive!!)");
            $this->restore();
            echo '</p>';
        }
    }

    private function install($lastVersion, $update_path, $archive)
    {
        try
        {
            $execute_commands = false;
            $upgrade_cmds_filename = 'upgrade.php';
            $upgrade_cmds_path = config('laraupdater.tmp_path').'/'.$upgrade_cmds_filename;

            $zipHandle = new \ZipArchive();
            if ($zipHandle->open($update_path, \ZipArchive::RDONLY) !== true) 
            {
                throw new \Exception(trans("laraupdater.Error_during_download."));
            }
            $archive = substr($archive,0, -4);

            echo '<p>'.trans("laraupdater.CHANGELOG").': </p>';

            $zipHandle->extractTo(config('laraupdater.tmp_path'));
            $zipHandle->close();

            if ($execute_commands == true)
            {
                include ($upgrade_cmds_path);

                if (main()) //upgrade-VERSION.php contains the 'main()' method with a BOOL return to check its execution.
                    echo '<p class="success">&raquo; '. trans("laraupdater.Commands_successfully_executed.") .'</p>';
                else
                    echo '<p class="danger">&raquo;'. trans("laraupdater.Error_during_commands_execution.") .'</p>';

                unlink($upgrade_cmds_path);
                File::delete($upgrade_cmds_path); //clean TMP
            }

            // copy folder
            File::copyDirectory(config('laraupdater.tmp_path').'/'.$archive, base_path());

            // call seeder
            Artisan::call('db:seed', ['--class' => 'DatabaseSeeder', '--force' => true]);
            Artisan::call('db:seed', ['--class' => 'ReferenceSeeder', '--force' => true]);
            Artisan::call('db:seed', ['--class' => 'PermissionSeeder', '--force' => true]);

            // update .env and config file
            $this->setEnvValue('APP_VERSION', 'app.version', $lastVersion);

            File::delete($update_path); //clean TMP
            File::deleteDirectory($this->tmp_backup_dir); //remove backup temp folder
            File::deleteDirectory(config('laraupdater.tmp_path').'/'.$archive); // remove exctract folder

        } catch (\Exception $e) { return false; }

        return true;
    }

    /*
    * Download Update from $update_baseurl to $tmp_path (local folder).
    */
    private function download($update_name)
    {
        try
        {
            $filename_tmp = config('laraupdater.tmp_path').'/'.$update_name;
            if ( !is_file( $filename_tmp ) ) 
            {
                $newUpdate = file_get_contents(config('laraupdater.update_baseurl').'/'.$update_name);
                $dlHandler = fopen($filename_tmp, 'w');
                if ( !fwrite($dlHandler, $newUpdate) )
                {
                    echo '<p>'.trans("laraupdater.Could_not_save_new_update").'</p>';
                    exit();
                }
            }
        } catch (\Exception $e) { 
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

    /*
    * Check if a new Update exist.
    */
    public function check()
    {
        $lastVersionInfo = $this->getLastVersion();
        if ( version_compare($lastVersionInfo['version'], $this->getCurrentVersion(), ">") )
            return $lastVersionInfo['version'];

        return '';
    }

    /*
    * Get Update description.
    */

    public function getDescription()
    {
        $content = $this->getLastVersion();
        $description = $this->getUpdateDesc();
        $html = '
            <h6>Rilis aplikasi terbaru versi: <b>'.$content['version'].'</b></h6><br/>
            <b>Log perubahan</b><br/><br/>'.$description;
        return $html;
    }

    private function getLastVersion()
    {
        $content = file_get_contents(config('laraupdater.update_baseurl').'/meta.json');
        return json_decode($content, true);
    }

    private function getUpdateDesc()
    {
        $Parsedown = new Parsedown();
        $content = file_get_contents(config('laraupdater.update_baseurl').'/description.md');
        return $Parsedown->text($content);
    }

    private function backup($filename)
    {
        $backup_dir = $this->tmp_backup_dir;

        if ( !is_dir($backup_dir) ) File::makeDirectory($backup_dir, $mode = 0755, true, true);
        if ( !is_dir($backup_dir.'/'.dirname($filename)) ) File::makeDirectory($backup_dir.'/'.dirname($filename), $mode = 0755, true, true);

        File::copy(base_path().'/'.$filename, $backup_dir.'/'.$filename); //to backup folder
    }

    private function restore()
    {
        if( !isset($this->tmp_backup_dir) )
            $this->tmp_backup_dir = base_path().'/backup_'.date('Ymd');

        try
        {
            $backup_dir = $this->tmp_backup_dir;
            $backup_files = File::allFiles($backup_dir);

            foreach ($backup_files as $file)
            {
                $filename = (string)$file;
                $filename = substr($filename, (strlen($filename)-strlen($backup_dir)-1)*(-1));
                echo $backup_dir.'/'.$filename." => ".base_path().'/'.$filename;
                File::copy($backup_dir.'/'.$filename, base_path().'/'.$filename); //to respective folder
            }

        } catch(\Exception $e) {
            echo "Exception => ".$e->getMessage();
            echo "<BR>[ ".trans("laraupdater.FAILED")." ]";
            echo "<BR> ".trans("laraupdater.Backup_folder_is_located_in:")." <i>".$backup_dir."</i>.";
            echo "<BR> ".trans("laraupdater.Remember_to_restore_System_UP-Status_through_shell_command:")." <i>php artisan up</i>.";
            return false;
        }

        echo "[ ".trans("laraupdater.RESTORED")." ]";
        return true;
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
            Artisan::call('config:cache');
        }
    }
}

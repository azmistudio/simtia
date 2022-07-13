<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Exception;

class RestoreDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:restore {name} {--path=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Restore application database';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $object = $this->argument('name');
        $path = $this->option('path');
        if (!empty($path))
        {
            try 
            {
                if (file_exists($path))
                {
                    $this->info('The restore has been started');
                    // reset database
                    $this->call('down');
                    switch ($object) 
                    {
                        case 'all':
                            $this->resetSchema('finance');
                            $this->resetSchema('academic');
                            $this->resetSchema('public');
                            break;
                        case 'academic':
                            $this->resetSchema('finance');
                            $this->resetSchema('academic');
                            break;
                        case 'finance':
                            $this->resetSchema('finance');
                            break;
                        default:
                            $process = new Process([]);
                            $process->start();
                            throw new ProcessFailedException($process);
                            break;
                    }
                    // 
                    $process = Process::fromShellCommandline($this->stringCommand());
                    $process->setWorkingDirectory($this->workingDir());
                    $process->run(null, $this->configConnection($path));
                    // 
                    $this->call('up');
                    $this->info('The restore has been proceed successfully.');
                } else {
                    $this->error('Lokasi file backup tidak tersedia.');
                }
            } catch (ProcessFailedException $exception) {
                logger()->error('Retore exception', compact('exception'));
                $this->error('The restore process has been failed.');
            }
        } else {
            $this->error('Lokasi file backup tidak tersedia.');
        }
        
    }

    private function stringCommand()
    {
        if (Str::contains($this->checkOS(),'Windows'))
        {
            $pg_password = 'SET PGPASSWORD="${:password}"&&psql.exe';
        } else {
            $pg_password = 'PGPASSWORD="${:password}" psql';
        }
        return $pg_password.' -U "${:username}" -h localhost -p "${:port}" "${:database}" < "${:input}"';
    }

    private function configConnection($path)
    {
        return [
            'password'  => config('database.connections.pgsql.password'),
            'username'  => config('database.connections.pgsql.username'),
            'port'      => config('database.connections.pgsql.port'),
            'database'  => config('database.connections.pgsql.database'),
            'input'     => $path,
        ];
    }

    private function workingDir()
    {
        if (Str::contains($this->checkOS(),'Windows'))
        {
            return 'C:\Program Files (x86)\PostgreSQL\10\bin';
        } else {
            return '/home';
        }
    }

    private function checkOS()
    {
        $process = new Process(['php','-r','echo php_uname();']);
        $process->run();
        return $process->getOutput();
    }

    private function resetSchema($schema)
    {
        $procedure = config('database.reset.'.$schema);
        return DB::unprepared($procedure);
    }
}

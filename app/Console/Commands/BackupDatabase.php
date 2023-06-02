<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class BackupDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:backup {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backup application database';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $schema = array('all','academic','finance','no_exclude');
        $object = $this->argument('name');
        if (in_array($object, $schema))
        {
            try 
            {
                $this->info('The backup has been started');
                $process = Process::fromShellCommandline($this->stringCommand($object));
                $process->setWorkingDirectory($this->workingDir());
                $process->run(null, $this->configConnection($object));
                // 
                $this->info('The backup has been proceed successfully.');
            } catch (ProcessFailedException $exception) {
                logger()->error('Backup exception', compact('exception'));
                $this->error('The backup process has been failed.');
            }
        } else {
            $this->info("Skema {$object} tidak ditemukan di database.");
        }
    }

    private function stringCommand($schema)
    {
        if (Str::contains($this->checkOS(),'Windows'))
        {
            $pg_password = 'SET PGPASSWORD="${:password}"&&pg_dump.exe';
        } else {
            $pg_password = 'PGPASSWORD="${:password}" pg_dump';
        }
        // 
        $include_schema_all      = '-n public -n academic -n finance';
        $include_schema_academic = '-n academic -n finance';
        $exclude_table_public    = '-T public.departments_id_seq -T public.departments -T public.institutes_id_seq -T public.institutes -T public.migrations_id_seq -T public.migrations -T public.references_id_seq -T public.references -T public.users_id_seq -T public.users -T public.references_id_seq -T public.references -T public.model_has_permissions_id_seq -T public.model_has_permissions -T public.model_has_roles_id_seq -T public.model_has_roles -T public.permissions_id_seq -T public.permissions -T public.roles_id_seq -T public.roles -T public.role_has_permissions_id_seq -T public.role_has_permissions -T public.employees_id_seq -T public.employees';
        $exclude_table_finance   = '-T finance.code_categories_id_seq -T finance.code_categories -T finance.codes_id_seq -T finance.codes';
        // 
        switch ($schema) 
        {
            case 'academic':
                return $pg_password.' -U "${:username}" -h localhost -p "${:port}" '.$include_schema_academic.' -a '.$exclude_table_finance.' "${:database}" >> "${:output}"';                
                break;
            case 'finance':
                return $pg_password.' -U "${:username}" -h localhost -p "${:port}" -n "${:schema}" -a '.$exclude_table_finance.' "${:database}" >> "${:output}"';                
                break;
            case 'all':
                return $pg_password.' -U "${:username}" -h localhost -p "${:port}" '.$include_schema_all.' -a '.$exclude_table_public.' '.$exclude_table_finance.' "${:database}" >> "${:output}"';                
                break;
            default:
                return $pg_password.' -U "${:username}" -h localhost -p "${:port}" '.$include_schema_all.' -a "${:database}" >> "${:output}"';                
                break;
        }
    }

    private function configConnection($schema)
    {
        switch ($schema) 
        {
            case 'all':
                return [
                    'password'  => config('database.connections.pgsql.password'),
                    'username'  => config('database.connections.pgsql.username'),
                    'port'      => config('database.connections.pgsql.port'),
                    'database'  => config('database.connections.pgsql.database'),
                    'output'    => storage_path(sprintf('app/backup/%s_'.config('database.connections.pgsql.database').'_%s.sql', $schema, now()->format('YmdHis'))),
                ];
                break;
            default:
                return [
                    'password'  => config('database.connections.pgsql.password'),
                    'username'  => config('database.connections.pgsql.username'),
                    'port'      => config('database.connections.pgsql.port'),
                    'schema'    => $schema,
                    'database'  => config('database.connections.pgsql.database'),
                    'output'    => storage_path(sprintf('app/backup/%s_'.config('database.connections.pgsql.database').'_%s.sql', $schema, now()->format('YmdHis'))),
                ];
                break;
        }
    }

    private function workingDir()
    {
        if (Str::contains($this->checkOS(),'Windows'))
        {
            return 'C:\Program Files (x86)\PostgreSQL\10\bin';
        } else {
            return '/';
        }
    }

    private function checkOS()
    {
        $process = new Process(['php','-r','echo php_uname();']);
        $process->run();
        return $process->getOutput();
    }
}

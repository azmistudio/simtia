<?php

namespace Modules\HR\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Pluralizer;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\File;
use Nwidart\Modules\Traits\ModuleCommandTrait;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class CreateRepositoryCommand extends Command
{
    use ModuleCommandTrait;

    /**
     * The name of argument name.
     *
     * @var string
     */
    protected $argumentName = 'name';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'module:make-repositories';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make repositories class.';

    public function getDefaultNamespace() : string
    {
        $module = $this->laravel['modules'];
        return $module->config('paths.generator.request.namespace') ?: $module->config('paths.generator.request.path', 'Http/Requests');
    }

    /**
     * Filesystem instance
     * @var Filesystem
     */
    protected $files;

    /**
     * Create a new command instance.
     * @param Filesystem $files
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();
        $this->files = $files;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $repository_path = $this->getSourcePath('repository');
        $eloquent_path = $this->getSourcePath('eloquent');
        if (File::exists($repository_path))
        {
            $this->info("Folder : {$this->getSingularClassName($this->argument('name'))} already exits");
        } else {
            $this->makeDirectory(dirname($repository_path));
            $repository = $this->getSourceFile();
            $eloquent = $this->getSourceFileEloquent();
            if (!$this->files->exists($repository_path)) 
            {
                $this->files->put($repository_path, $repository);
                $this->files->put($eloquent_path, $eloquent);
                $this->info("File : {$repository_path} created");
            } else {
                $this->info("File : {$repository_path} already exits");
            }
        }
    }

    /**
     * Return the stub file path
     * @return string
     *
     */
    public function getStubPath()
    {
        return __DIR__ . '/../../../stubs/repositories/repository.stub';
    }

    /**
     * Return the stub file path
     * @return string
     *
     */
    public function getStubEloquentPath()
    {
        return __DIR__ . '/../../../stubs/repositories/eloquent.stub';
    }

    /**
    **
    * Map the stub variables present in stub to its value
    *
    * @return array
    *
    */
    public function getStubVariables()
    {
        return [
            'NAMESPACE'  => 'Modules\\'.$this->argument('module').'\\Repositories\\' . $this->getSingularClassName($this->argument('name')),
            'CLASS_NAME' => $this->getSingularClassName($this->argument('name')),
        ];
    }

    /**
     * Get the stub path and the stub variables
     *
     * @return bool|mixed|string
     *
     */
    public function getSourceFile()
    {
        return $this->getStubContents($this->getStubPath(), $this->getStubVariables());
    }

    /**
     * Get the stub path and the stub variables
     *
     * @return bool|mixed|string
     *
     */
    public function getSourceFileEloquent()
    {
        return $this->getStubContents($this->getStubEloquentPath(), $this->getStubVariables());
    }

    /**
     * Replace the stub variables(key) with the desire value
     *
     * @param $stub
     * @param array $stubVariables
     * @return bool|mixed|string
     */
    public function getStubContents($stub , $stubVariables = [])
    {
        $contents = file_get_contents($stub);
        foreach ($stubVariables as $search => $replace)
        {
            $contents = str_replace('$'.$search.'$' , $replace, $contents);
        }
        return $contents;
    }

    /**
     * Get the full path of generate class
     *
     * @return string
     */
    public function getSourcePath($param)
    {
        if ($param == 'repository')
        {
            return base_path('Modules\\'. $this->argument('module') . '\\Repositories') .'\\' .$this->getSingularClassName($this->argument('name')) . '\\' .$this->getSingularClassName($this->argument('name')) . 'Repository.php';
        } else {
            return base_path('Modules\\'. $this->argument('module') . '\\Repositories') .'\\' .$this->getSingularClassName($this->argument('name')) . '\\' .$this->getSingularClassName($this->argument('name')) . 'Eloquent.php';
        }
    }

    /**
     * Return the Singular Capitalize Name
     * @param $name
     * @return string
     */
    public function getSingularClassName($name)
    {
        return ucwords(Pluralizer::singular($name));
    }

    /**
     * Build the directory for the class if necessary.
     *
     * @param  string  $path
     * @return string
     */
    protected function makeDirectory($path)
    {
        if (! $this->files->isDirectory($path)) {
            $this->files->makeDirectory($path, 0777, true, true);
        }
        return $path;
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the form request class.'],
            ['module', InputArgument::OPTIONAL, 'The name of module will be used.'],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
        ];
    }
}

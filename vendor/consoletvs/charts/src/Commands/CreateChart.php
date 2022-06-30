<?php

declare(strict_types=1);

namespace ConsoleTVs\Charts\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;

class CreateChart extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'make:chart {name : Determines the chart name}';

    /**
     * The console command description.
     */
    protected $description = 'Create a new chart class';

    /**
     * The type of class being generated.
     */
    protected $type = 'Chart';

    /**
     * Gets the stub to generate the chart.
     */
    protected function getStub()
    {
        return __DIR__.'/stubs/chart.stub';
    }

    /**
     * Determines the namespace where the charts will be
     * created at.
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Charts';
    }

    /**
     * Determines the name of the stub to generate.
     */
    protected function getNameInput()
    {
        return Str::of($this->argument('name'))
            ->trim()
            ->camel()
            ->ucfirst();
    }
}

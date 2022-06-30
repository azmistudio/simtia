<?php

declare(strict_types=1);

namespace ConsoleTVs\Charts;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\Registrar as RouteRegistrar;
use Illuminate\Support\Str;

class Registrar
{
    /**
     * Stores the application container that
     * will be used to resolve chart classes.
     */
    private Application $app;

    /**
     * Stores the application configuration
     * repository that will be used to get the
     * user-defined configuration.
     */
    private Repository $config;

    /**
     * Stores the route registrar that will be
     * used to register the application routes.
     */
    private RouteRegistrar $route;

    /**
     * Creates a new instance of the Chart Registrar.
     * This class is defined as a sigleton in the
     * application container.
     */
    public function __construct(Application $app, Repository $config, RouteRegistrar $route)
    {
        $this->app = $app;
        $this->config = $config;
        $this->route = $route;
    }

    /**
     * Registers new charts into the application.
     */
    public function register(array $charts): void
    {
        $globalRoutePrefix = $this->config->get('charts.global_route_prefix', 'api/chart');
        $globalMiddlewares = $this->config->get('charts.global_middlewares', []);
        $globalRouteNamePrefix = $this->config->get('charts.global_route_name_prefix', 'charts');
        $globalPrefixArray = Str::of($globalRoutePrefix)->explode('/')->filter()->values();

        foreach ($charts as $chartClass) {
            // Create the chart instance.
            $instance = $this->app->make($chartClass);
            // Get the name of the chart by using the instance name or the class name.
            $name = $instance->name ?? Str::snake(class_basename($chartClass));
            // Clean the prefix and transform it into an array for concatenation.
            $prefixArray = Str::of($instance->prefix ?? '')->explode('/')->filter()->values();
            // Define the route name for the given chart.
            $routeName = $instance->routeName ?? $name;
            // Register the route for the given chart.
            $this->route
                ->prefix($globalPrefixArray->merge($prefixArray)->implode('/'))
                ->middleware([...$globalMiddlewares, ...($instance->middlewares ?? [])])
                ->name("{$globalRouteNamePrefix}.{$routeName}")
                ->get($name, 'ConsoleTVs\Charts\ChartsController')
                ->defaults('chart', $instance);
        }
    }
}

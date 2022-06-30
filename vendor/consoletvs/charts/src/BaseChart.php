<?php

declare(strict_types=1);

namespace ConsoleTVs\Charts;

use Chartisan\PHP\Chartisan;
use Illuminate\Http\Request;
use ReflectionClass;

abstract class BaseChart
{
    /**
     * Determines the chart name to be used on the
     * route. If null, the name will be a snake_case
     * version of the class name.
     */
    public ?string $name;

    /**
     * Determines the name suffix of the chart route.
     * This will also be used to get the chart URL
     * from the blade directrive. If null, the chart
     * name will be used.
     */
    public ?string $routeName;

    /**
     * Determines the prefix that will be used by the chart
     * endpoint.
     */
    public ?string $prefix;

    /**
     * Determines the middlewares that will be applied
     * to the chart endpoint.
     */
    public ?array $middlewares;

    /**
     * Handles the HTTP request of the chart. This must always
     * return the chart instance. Do not return a string or an array.
     */
    abstract public function handler(Request $request): Chartisan;

    public static function __set_state(array $properties)
    {
        $klass = new static();

        $refClass = new ReflectionClass($klass);

        foreach ($properties as $name => $value) {
            $property = $refClass->getProperty($name);
            $property->setAccessible(true);

            $property->setValue($klass, $value);
        }

        return $klass;
    }
}

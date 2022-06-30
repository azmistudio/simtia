<?php

declare(strict_types = 1);

namespace Chartisan\PHP;

/**
 * ServerData represents how the server is expected
 * to send the data to the chartisan client.
 */
class ServerData
{
    /**
     * Stores the chart information.
     *
     * @var ChartData
     */
    public ChartData $chart;

    /**
     * Stores the datasets of the chart.
     *
     * @var DatasetData[]
     */
    public array $datasets = [];

    /**
     * Creates a new instance of a server data.
     */
    public function __construct()
    {
        $this->chart = new ChartData;
    }
}

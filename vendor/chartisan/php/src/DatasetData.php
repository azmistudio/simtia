<?php

declare(strict_types = 1);

namespace Chartisan\PHP;

/**
 * Represents the dataset information.
 */
class DatasetData
{
    /**
     * Stores the dataset name.
     *
     * @var string
     */
    public string $name;

    /**
     * Stores the dataset values.
     *
     * @var array[float]
     */
    public array $values;

    /**
     * Stores the dataset extra information if needed.
     *
     * @var ?array
     */
    public ?array $extra;

    /**
     * Creates a new instance of DatasetData.
     *
     * @param string $name
     * @param array $values
     * @param array|null $extra
     */
    public function __construct(string $name, array $values, ?array $extra)
    {
        $this->name = $name;
        $this->values = $values;
        $this->extra = $extra;
    }
}

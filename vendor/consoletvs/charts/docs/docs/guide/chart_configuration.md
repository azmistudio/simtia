# Chart Configuration

You can customize how some chart configuration on the laravel side by simply applying some of the
class properties needed.

You can modify the following props on the chart:

- Middlewares
- Chart name (used to generate the URL)
- Chart route name
- Route endpoint prefix

```php
<?php

declare(strict_types = 1);

namespace App\Charts;

use ConsoleTVs\Charts\BaseChart;
use Illuminate\Http\Request;
use Chartisan\PHP\Chartisan;

class SampleChart extends BaseChart
{
    /**
     * Determines the chart name to be used on the
     * route. If null, the name will be a snake_case
     * version of the class name.
     */
    public ?string $name = 'custom_chart_name';

    /**
     * Determines the name suffix of the chart route.
     * This will also be used to get the chart URL
     * from the blade directrive. If null, the chart
     * name will be used.
     */
    public ?string $routeName = 'chart_route_name';

    /**
     * Determines the prefix that will be used by the chart
     * endpoint.
     */
    public ?string $prefix = 'some_prefix';

    /**
     * Determines the middlewares that will be applied
     * to the chart endpoint.
     */
    public ?array $middlewares = ['auth'];

    /**
     * Handles the HTTP request for the given chart.
     * It must always return an instance of Chartisan
     * and never a string or an array.
     */
    public function handler(Request $request): Chartisan
    {
        return Chartisan::build()
            ->labels(['First', 'Second', 'Third'])
            ->dataset('Sample', [1, 2, 3])
            ->dataset('Sample 2', [3, 2, 1]);
    }
}
```

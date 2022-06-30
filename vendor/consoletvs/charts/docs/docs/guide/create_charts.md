# Create Charts

You can start creating charts with the typical `make` command by laravel artisan.

Following the other make conventions, you may use the following command to create a new chart and give it
a name. The name will be used by the class and also the route name. You may further change this in the
created class.

```
php artisan make:chart SampleChart
```

This will create a SampleChart class under `App\Charts` namespace that will look like this:

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

## Create the Chartisan instance

The handler method is the one that will be called when Chartisan tries to get the chart
data. You'll get the request instance as a parameter in case you want to check for query parameters
or additional info like headers, or post data. You can modify the Chartisan instance as you need.

You can know more about Chartisan at the [Documentation](https://chartisan.dev) page.

You have to return a Chartisan instance, never a string or an object.

## Register the chart

You'll need to manually register using the `App\Providers\AppServiceProvider`

Laravel charts have a registered singleton that will be injected to the `boot()` method on the service provider.

You can use the following example as a guide to register an example chart.

```php
<?php

namespace App\Providers;

use ConsoleTVs\Charts\Registrar as Charts;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    // ...

    /**
     * Bootstrap any application services.
     */
    public function boot(Charts $charts)
    {
        $charts->register([
            \App\Charts\SampleChart::class
        ]);
    }
}
```

## Generated routes

You can use php artisan route:list -c to see all your application routes and check out the chart routes that have
been created by Laravel Charts in case you need them.

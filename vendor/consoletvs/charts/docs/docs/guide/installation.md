# Installation

You can install Laravel Charts by using [Composer](https://getcomposer.org/)

You can use the following composer command to install it into an existing laravel project.

```
composer require consoletvs/charts:7.*
```

Laravel will already register the service provider to your application because laravel charts
does make use of the extra laravel tag on the `composer.json` schema.

## Publish the configuration file

You can publish the configuration file of Laravel charts by running the following command:

```
php artisan vendor:publish --tag=charts
```

This will create a new file under `app/config/charts.php` that you can edit to modify some of the
options of Laravel Charts.

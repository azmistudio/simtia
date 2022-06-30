<?php

declare(strict_types = 1);

/**
 * Register the auto loader.
 */
require __DIR__ . '/../vendor/autoload.php';

/**
 * Get the application response.
 */
$response = require_once __DIR__ . '/../app.php';

/**
 * Respond to the request.
 */
echo $response();

<?php
/**
 * Public entry point - Single entry point for the application.
 */

// Define base path
define('BASE_PATH', dirname(__DIR__));

// Autoload
require BASE_PATH . '/vendor/autoload.php';

// Ensure UTF-8 encoding
header('Content-Type: text/html; charset=UTF-8');

// Boot application
$app = new \App\Core\App();
$app->run();

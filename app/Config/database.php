<?php
/**
 * Database configuration.
 */

return [
    'connection' => env('DB_CONNECTION', 'sqlite'),
    'database' => env('DB_DATABASE', 'database/database.sqlite'),
];

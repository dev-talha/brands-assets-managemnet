<?php
/**
 * Application configuration.
 */

return [
    'name' => env('APP_NAME', 'Brand CDN Manager'),
    'env' => env('APP_ENV', 'local'),
    'debug' => env('APP_DEBUG', 'true') === 'true',
    'url' => env('APP_URL', 'http://localhost:8000'),
    'cdn_base_url' => env('CDN_BASE_URL', 'http://localhost:8000'),

    'upload' => [
        'max_mb' => (int) env('UPLOAD_MAX_MB', '50'),
        'image_max_mb' => (int) env('IMAGE_MAX_MB', '10'),
        'zip_max_mb' => (int) env('ZIP_MAX_MB', '100'),
    ],
];

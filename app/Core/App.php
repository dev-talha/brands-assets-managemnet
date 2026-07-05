<?php
/**
 * App - Application bootstrap.
 */

namespace App\Core;

class App
{
    private Router $router;

    public function __construct()
    {
        $this->loadEnv();
        $this->startSession();
        Response::setSecurityHeaders();
        $this->router = new Router();
    }

    private function loadEnv(): void
    {
        $envFile = BASE_PATH . '/.env';
        if (!file_exists($envFile)) {
            // Copy from example
            $example = BASE_PATH . '/.env.example';
            if (file_exists($example)) {
                copy($example, $envFile);
            }
        }
        if (file_exists($envFile)) {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                $line = trim($line);
                if (str_starts_with($line, '#')) continue;
                if (!str_contains($line, '=')) continue;
                [$key, $value] = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value, " \t\n\r\0\x0B\"'");
                $_ENV[$key] = $value;
                putenv("{$key}={$value}");
            }
        }
    }

    private function startSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            $secure = env('SESSION_SECURE', 'false') === 'true';
            $samesite = env('SESSION_SAMESITE', 'Lax');

            session_set_cookie_params([
                'lifetime' => 0,
                'path' => '/',
                'domain' => '',
                'secure' => $secure,
                'httponly' => true,
                'samesite' => $samesite,
            ]);
            session_start();
        }
    }

    public function getRouter(): Router
    {
        return $this->router;
    }

    public function run(): void
    {
        $request = new Request();

        // Load routes
        $router = $this->router;
        require BASE_PATH . '/routes/web.php';

        ob_start();
        $router->dispatch($request);
        $output = ob_get_clean();

        // Rewrite root-relative URLs if APP_URL is configured
        $appUrl = env('APP_URL', '');
        if ($appUrl) {
            $base = rtrim($appUrl, '/');
            // Safely replace href="/, src="/, action="/ avoiding protocol-relative URLs like href="//
            $output = preg_replace('/(href|src|action)="\/(?!\/)/i', '$1="' . $base . '/', $output);
        }

        echo $output;
    }
}

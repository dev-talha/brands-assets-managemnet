<?php
/**
 * Response - HTTP response helpers.
 */

namespace App\Core;

class Response
{
    public static function json(array $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    public static function redirect(string $url, int $status = 302): void
    {
        if (str_starts_with($url, '/') && !str_starts_with($url, '//')) {
            $base = rtrim(env('APP_URL', ''), '/');
            if ($base) {
                $url = $base . $url;
            }
        }
        
        http_response_code($status);
        header('Location: ' . $url);
        exit;
    }

    public static function download(string $filePath, string $filename, string $mimeType): void
    {
        if (!file_exists($filePath)) {
            http_response_code(404);
            echo 'File not found';
            exit;
        }
        header('Content-Type: ' . $mimeType);
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . filesize($filePath));
        header('Cache-Control: no-cache');
        readfile($filePath);
        exit;
    }

    public static function stream(string $filePath, string $filename, string $mimeType, array $cacheHeaders = []): void
    {
        if (!file_exists($filePath)) {
            http_response_code(404);
            echo 'File not found';
            exit;
        }

        header('Content-Type: ' . $mimeType);
        header('Content-Disposition: inline; filename="' . $filename . '"');
        header('Content-Length: ' . filesize($filePath));
        header('X-Content-Type-Options: nosniff');

        foreach ($cacheHeaders as $header) {
            header($header);
        }

        $fp = fopen($filePath, 'rb');
        fpassthru($fp);
        fclose($fp);
        exit;
    }

    public static function error(int $status, string $message = ''): void
    {
        http_response_code($status);
        if (class_exists(\App\Core\View::class) && file_exists(BASE_PATH . "/app/Views/errors/{$status}.php")) {
            \App\Core\View::render("errors.{$status}", ['message' => $message], 'public');
        } elseif (file_exists(BASE_PATH . "/app/Views/errors/{$status}.php")) {
            include BASE_PATH . "/app/Views/errors/{$status}.php";
        } else {
            echo "<h1>{$status}</h1><p>{$message}</p>";
        }
        exit;
    }

    public static function setSecurityHeaders(): void
    {
        header('X-Frame-Options: SAMEORIGIN');
        header('X-Content-Type-Options: nosniff');
        header('Referrer-Policy: strict-origin-when-cross-origin');
        header("Permissions-Policy: camera=(), microphone=(), geolocation=()");
    }
}

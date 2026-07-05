<?php
/**
 * Request - Wrapper for HTTP request data.
 */

namespace App\Core;

class Request
{
    private array $get;
    private array $post;
    private array $files;
    private array $server;
    private array $cookies;

    public function __construct()
    {
        $this->get = $_GET;
        $this->post = $_POST;
        $this->files = $_FILES;
        $this->server = $_SERVER;
        $this->cookies = $_COOKIE;
    }

    public function method(): string
    {
        return strtoupper($this->server['REQUEST_METHOD'] ?? 'GET');
    }

    public function uri(): string
    {
        $uri = parse_url($this->server['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        
        // 1. Support for subdirectory hosting using APP_URL
        $appUrl = env('APP_URL', '');
        if ($appUrl) {
            $appUrlPath = rtrim(parse_url($appUrl, PHP_URL_PATH) ?? '', '/');
            if ($appUrlPath && str_starts_with($uri, $appUrlPath)) {
                $uri = substr($uri, strlen($appUrlPath));
                return rtrim($uri, '/') ?: '/';
            }
        }
        
        // 2. Fallback for Apache mod_rewrite when APP_URL isn't fully accurate
        $scriptName = $this->server['SCRIPT_NAME'] ?? '';
        $baseDir = str_replace('\\', '/', dirname($scriptName));
        
        if ($baseDir !== '/' && str_starts_with($uri, $baseDir)) {
            $uri = substr($uri, strlen($baseDir));
        } else {
            $parentDir = str_replace('\\', '/', dirname($baseDir));
            if ($parentDir !== '/' && str_starts_with($uri, $parentDir)) {
                $uri = substr($uri, strlen($parentDir));
            }
        }
        
        return rtrim($uri, '/') ?: '/';
    }

    public function get(string $key, $default = null)
    {
        return $this->get[$key] ?? $default;
    }

    public function post(string $key, $default = null)
    {
        return $this->post[$key] ?? $default;
    }

    public function input(string $key, $default = null)
    {
        return $this->post[$key] ?? $this->get[$key] ?? $default;
    }

    public function all(): array
    {
        return array_merge($this->get, $this->post);
    }

    public function file(string $key): ?array
    {
        return $this->files[$key] ?? null;
    }

    public function files(string $key): array
    {
        if (!isset($this->files[$key])) {
            return [];
        }
        $files = $this->files[$key];
        // Normalize multiple file uploads
        if (is_array($files['name'])) {
            $normalized = [];
            $count = count($files['name']);
            for ($i = 0; $i < $count; $i++) {
                $normalized[] = [
                    'name' => $files['name'][$i],
                    'type' => $files['type'][$i],
                    'tmp_name' => $files['tmp_name'][$i],
                    'error' => $files['error'][$i],
                    'size' => $files['size'][$i],
                ];
            }
            return $normalized;
        }
        return [$files];
    }

    public function hasFile(string $key): bool
    {
        $file = $this->file($key);
        if (!$file) return false;
        if (is_array($file['error'])) {
            return $file['error'][0] !== UPLOAD_ERR_NO_FILE;
        }
        return $file['error'] !== UPLOAD_ERR_NO_FILE;
    }

    public function header(string $key): ?string
    {
        $serverKey = 'HTTP_' . str_replace('-', '_', strtoupper($key));
        return $this->server[$serverKey] ?? null;
    }

    public function ip(): string
    {
        return $this->server['REMOTE_ADDR'] ?? '127.0.0.1';
    }

    public function userAgent(): string
    {
        return $this->server['HTTP_USER_AGENT'] ?? '';
    }

    public function referer(): string
    {
        return $this->server['HTTP_REFERER'] ?? '';
    }

    public function isAjax(): bool
    {
        return $this->header('X-Requested-With') === 'XMLHttpRequest';
    }

    public function query(): array
    {
        return $this->get;
    }
}

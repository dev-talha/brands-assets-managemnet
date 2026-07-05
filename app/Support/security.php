<?php
/**
 * Security helpers - CSRF, sanitization, rate limiting.
 */

function verifyCsrfToken(): bool
{
    $token = $_POST['_csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    $sessionToken = $_SESSION['_csrf_token'] ?? '';
    if (empty($token) || empty($sessionToken)) return false;
    return hash_equals($sessionToken, $token);
}

function regenerateCsrfToken(): void
{
    $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
}

function sanitizeInput(string $input): string
{
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

function sanitizeSvg(string $content): string
{
    // Remove script tags
    $content = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $content);
    // Remove event handlers
    $content = preg_replace('/\s+on\w+\s*=\s*["\'][^"\']*["\']/i', '', $content);
    // Remove javascript: URLs
    $content = preg_replace('/href\s*=\s*["\']javascript:[^"\']*["\']/i', '', $content);
    return $content;
}

function checkRateLimit(string $key, int $maxAttempts, int $windowSeconds): bool
{
    $now = time();
    $cacheKey = 'rate_limit_' . $key;

    if (!isset($_SESSION[$cacheKey])) {
        $_SESSION[$cacheKey] = [];
    }

    // Remove old attempts
    $_SESSION[$cacheKey] = array_filter(
        $_SESSION[$cacheKey],
        fn($timestamp) => ($now - $timestamp) < $windowSeconds
    );

    if (count($_SESSION[$cacheKey]) >= $maxAttempts) {
        return false; // Rate limited
    }

    $_SESSION[$cacheKey][] = $now;
    return true; // Allowed
}

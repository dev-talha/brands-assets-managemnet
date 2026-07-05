<?php

namespace App\Services;

use App\Models\User;

class AuthService
{
    public static function attempt(string $email, string $password): ?array
    {
        $user = User::findByEmail($email);
        if (!$user) return null;
        if ($user['status'] !== 'active') return null;

        if (!password_verify($password, $user['password_hash'])) {
            return null;
        }

        // Update last login
        User::updateLastLogin($user['id']);

        // Store user in session (without password hash)
        $sessionUser = $user;
        unset($sessionUser['password_hash']);
        $_SESSION['user'] = $sessionUser;

        return $sessionUser;
    }

    public static function logout(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params['path'], $params['domain'],
                $params['secure'], $params['httponly']
            );
        }
        session_destroy();
    }

    public static function check(): bool
    {
        return isset($_SESSION['user']);
    }

    public static function user(): ?array
    {
        return $_SESSION['user'] ?? null;
    }

    public static function refreshSessionUser(): void
    {
        $user = self::user();
        if ($user) {
            $fresh = User::findById($user['id']);
            if ($fresh) {
                unset($fresh['password_hash']);
                $_SESSION['user'] = $fresh;
            }
        }
    }
}

<?php
/**
 * Base Controller - Provides view rendering, redirect, and JSON helpers.
 */

namespace App\Core;

class Controller
{
    protected Request $request;

    public function __construct()
    {
        $this->request = new Request();
    }

    protected function view(string $view, array $data = [], string $layout = 'app'): void
    {
        View::render($view, $data, $layout);
    }

    protected function redirect(string $url): void
    {
        Response::redirect($url);
    }

    protected function json(array $data, int $status = 200): void
    {
        Response::json($data, $status);
    }

    protected function back(): void
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? '/admin/dashboard';
        Response::redirect($referer);
    }

    protected function withFlash(string $type, string $message): void
    {
        $_SESSION['flash'] = ['type' => $type, 'message' => $message];
    }

    protected function withErrors(array $errors): void
    {
        $_SESSION['errors'] = $errors;
    }

    protected function withOld(array $data): void
    {
        $_SESSION['old'] = $data;
    }

    protected function authorize(array $allowedRoles = []): void
    {
        if (empty($allowedRoles)) return;
        $user = currentUser();
        if (!$user || !in_array($user['role'], $allowedRoles)) {
            Response::error(403, 'Access denied');
        }
    }
}

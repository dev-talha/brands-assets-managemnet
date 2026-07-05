<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\View;
use App\Services\AuthService;
use App\Services\AuditService;

class AuthController extends Controller
{
    public function showLogin(): void
    {
        if (isLoggedIn()) {
            $this->redirect('/admin/dashboard');
        }
        View::render('auth.login', ['pageTitle' => 'Login'], 'auth');
    }

    public function login(): void
    {
        $email = trim($this->request->post('email', ''));
        $password = $this->request->post('password', '');

        // Fetch security settings
        $maxAttempts = 3;
        $lockoutMinutes = 30;
        $settingsRows = db()->query("SELECT key, value FROM settings WHERE key IN ('login_max_attempts', 'login_lockout_minutes')")->fetchAll();
        foreach ($settingsRows as $row) {
            if ($row['key'] === 'login_max_attempts' && is_numeric($row['value'])) {
                $maxAttempts = (int) $row['value'];
            }
            if ($row['key'] === 'login_lockout_minutes' && is_numeric($row['value'])) {
                $lockoutMinutes = (int) $row['value'];
            }
        }
        $lockoutSeconds = $lockoutMinutes * 60;

        // Rate limiting
        $rateLimitKey = 'login_' . md5($email . $this->request->ip());
        if (!checkRateLimit($rateLimitKey, $maxAttempts, $lockoutSeconds)) {
            $this->withFlash('error', "Too many login attempts. Please wait {$lockoutMinutes} minutes to protect against attacks.");
            $this->withOld(['email' => $email]);
            $this->redirect('/login');
        }

        if (empty($email) || empty($password)) {
            $this->withFlash('error', 'Please enter email and password.');
            $this->withOld(['email' => $email]);
            $this->redirect('/login');
        }

        $user = AuthService::attempt($email, $password);
        if (!$user) {
            AuditService::log('login_failure', 'user', null, null, $email);
            $this->withFlash('error', 'Invalid email or password.');
            $this->withOld(['email' => $email]);
            $this->redirect('/login');
        }

        AuditService::log('login_success', 'user', $user['id']);

        // Regenerate session ID
        session_regenerate_id(true);

        $intended = $_SESSION['intended_url'] ?? '/admin/dashboard';
        unset($_SESSION['intended_url']);
        $this->redirect($intended);
    }

    public function logout(): void
    {
        $user = currentUser();
        if ($user) {
            AuditService::log('logout', 'user', $user['id']);
        }
        AuthService::logout();
        // Start new session for flash message
        session_start();
        $this->withFlash('success', 'You have been logged out.');
        $this->redirect('/login');
    }
}

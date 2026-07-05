<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;

class ProfileController extends Controller
{
    public function index(): void
    {
        $this->view('profile.index', [
            'pageTitle' => 'My Profile',
            'user' => currentUser()
        ]);
    }

    public function updatePassword(): void
    {
        $newPassword = $this->request->post('new_password');
        $confirmPassword = $this->request->post('confirm_password');

        if (empty($newPassword) || empty($confirmPassword)) {
            $this->withFlash('error', 'Both password fields are required.');
            $this->redirect('/admin/profile');
        }

        if ($newPassword !== $confirmPassword) {
            $this->withFlash('error', 'New password and confirm password do not match.');
            $this->redirect('/admin/profile');
        }

        $user = currentUser();

        // Update password
        $hash = password_hash($newPassword, PASSWORD_DEFAULT);
        db()->query('UPDATE users SET password_hash = ?, updated_at = ? WHERE id = ?', [$hash, now(), $user['id']]);

        $this->withFlash('success', 'Password updated successfully.');
        $this->redirect('/admin/profile');
    }
}

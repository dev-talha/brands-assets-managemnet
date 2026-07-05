<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;

class UserController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        // Only super_admin can manage users
        $this->authorize(['super_admin']);
    }

    public function index(): void
    {
        $users = User::all();
        $this->view('users.index', [
            'pageTitle' => 'User Management',
            'users' => $users
        ]);
    }

    public function create(): void
    {
        $this->view('users.create', [
            'pageTitle' => 'Create User'
        ]);
    }

    public function store(): void
    {
        $name = $this->request->post('name');
        $email = $this->request->post('email');
        $password = $this->request->post('password');
        $role = $this->request->post('role', 'viewer');

        if (empty($name) || empty($email) || empty($password)) {
            $this->withFlash('error', 'Name, email, and password are required.');
            $this->redirect('/admin/users/create');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->withFlash('error', 'Invalid email format.');
            $this->redirect('/admin/users/create');
        }

        if (User::findByEmail($email)) {
            $this->withFlash('error', 'Email already exists.');
            $this->redirect('/admin/users/create');
        }

        User::create([
            'name' => $name,
            'email' => strtolower($email),
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
            'role' => $role,
            'status' => 'active'
        ]);

        $this->withFlash('success', 'User created successfully.');
        $this->redirect('/admin/users');
    }

    public function edit(int $id): void
    {
        $user = User::findById($id);
        if (!$user) {
            $this->withFlash('error', 'User not found.');
            $this->redirect('/admin/users');
        }

        $this->view('users.edit', [
            'pageTitle' => 'Edit User',
            'editUser' => $user
        ]);
    }

    public function update(int $id): void
    {
        $user = User::findById($id);
        if (!$user) {
            $this->withFlash('error', 'User not found.');
            $this->redirect('/admin/users');
        }

        $name = $this->request->post('name');
        $email = $this->request->post('email');
        $role = $this->request->post('role');
        $status = $this->request->post('status');
        $password = $this->request->post('password');

        if (empty($name) || empty($email) || empty($role) || empty($status)) {
            $this->withFlash('error', 'Name, email, role, and status are required.');
            $this->redirect("/admin/users/{$id}/edit");
        }

        $existing = User::findByEmail($email);
        if ($existing && $existing['id'] !== $id) {
            $this->withFlash('error', 'Email already in use by another user.');
            $this->redirect("/admin/users/{$id}/edit");
        }

        $updateData = [
            'name' => $name,
            'email' => strtolower($email),
            'role' => $role,
            'status' => $status
        ];

        if (!empty($password)) {
            $updateData['password_hash'] = password_hash($password, PASSWORD_DEFAULT);
        }

        User::update($id, $updateData);

        $this->withFlash('success', 'User updated successfully.');
        $this->redirect('/admin/users');
    }

    public function delete(int $id): void
    {
        $user = User::findById($id);
        if (!$user) {
            $this->withFlash('error', 'User not found.');
            $this->redirect('/admin/users');
        }

        if ($id === currentUser()['id']) {
            $this->withFlash('error', 'You cannot delete yourself.');
            $this->redirect('/admin/users');
        }

        User::delete($id);

        $this->withFlash('success', 'User deleted successfully.');
        $this->redirect('/admin/users');
    }
}

<?php

namespace App\Models;

class User
{
    public static function findByEmail(string $email): ?array
    {
        return db()->query(
            'SELECT * FROM users WHERE email = ? AND deleted_at IS NULL',
            [$email]
        )->fetch() ?: null;
    }

    public static function findById(int $id): ?array
    {
        return db()->query(
            'SELECT * FROM users WHERE id = ? AND deleted_at IS NULL',
            [$id]
        )->fetch() ?: null;
    }

    public static function all(): array
    {
        return db()->query(
            'SELECT * FROM users WHERE deleted_at IS NULL ORDER BY name ASC'
        )->fetchAll();
    }

    public static function create(array $data): int
    {
        $data['created_at'] = now();
        $data['updated_at'] = now();
        return db()->insert('users', $data);
    }

    public static function update(int $id, array $data): void
    {
        $data['updated_at'] = now();
        db()->update('users', $data, 'id = ?', [$id]);
    }

    public static function updateLastLogin(int $id): void
    {
        db()->update('users', ['last_login_at' => now(), 'updated_at' => now()], 'id = ?', [$id]);
    }

    public static function count(): int
    {
        return (int) db()->query('SELECT COUNT(*) as cnt FROM users WHERE deleted_at IS NULL')->fetch()['cnt'];
    }

    public static function delete(int $id): void
    {
        db()->update('users', ['deleted_at' => now()], 'id = ?', [$id]);
    }
}

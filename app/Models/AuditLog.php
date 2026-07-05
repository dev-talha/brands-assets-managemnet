<?php

namespace App\Models;

class AuditLog
{
    public static function create(array $data): int
    {
        $data['created_at'] = now();
        return db()->insert('audit_logs', $data);
    }

    public static function all(int $limit = 50, int $offset = 0): array
    {
        return db()->query(
            'SELECT a.*, u.name as user_name, u.email as user_email
             FROM audit_logs a
             LEFT JOIN users u ON u.id = a.user_id
             ORDER BY a.created_at DESC
             LIMIT ? OFFSET ?',
            [$limit, $offset]
        )->fetchAll();
    }

    public static function count(): int
    {
        return (int) db()->query('SELECT COUNT(*) as cnt FROM audit_logs')->fetch()['cnt'];
    }

    public static function findByEntity(string $entityType, int $entityId): array
    {
        return db()->query(
            'SELECT a.*, u.name as user_name
             FROM audit_logs a
             LEFT JOIN users u ON u.id = a.user_id
             WHERE a.entity_type = ? AND a.entity_id = ?
             ORDER BY a.created_at DESC',
            [$entityType, $entityId]
        )->fetchAll();
    }
}

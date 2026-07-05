<?php

namespace App\Models;

class Company
{
    private static function mapCompany(array $row): array
    {
        if (isset($row['social_links'])) {
            $row['social_links_array'] = json_decode($row['social_links'], true) ?: [];
        } else {
            $row['social_links_array'] = [];
        }
        return $row;
    }

    public static function all(array $filters = []): array
    {
        $sql = 'SELECT * FROM companies WHERE deleted_at IS NULL';
        $params = [];

        if (!empty($filters['search'])) {
            $sql .= ' AND (name LIKE ? OR domain LIKE ? OR slug LIKE ?)';
            $q = '%' . $filters['search'] . '%';
            $params = array_merge($params, [$q, $q, $q]);
        }

        if (isset($filters['status'])) {
            $sql .= ' AND status = ?';
            $params[] = $filters['status'];
        }

        if (isset($filters['is_public'])) {
            $sql .= ' AND is_public = ?';
            $params[] = (int) $filters['is_public'];
        }

        $sql .= ' ORDER BY name ASC';

        if (isset($filters['limit'])) {
            $sql .= ' LIMIT ?';
            $params[] = (int) $filters['limit'];
            if (isset($filters['offset'])) {
                $sql .= ' OFFSET ?';
                $params[] = (int) $filters['offset'];
            }
        }

        $results = db()->query($sql, $params)->fetchAll();
        return array_map([self::class, 'mapCompany'], $results);
    }

    public static function findById(int $id): ?array
    {
        $row = db()->query(
            'SELECT * FROM companies WHERE id = ? AND deleted_at IS NULL',
            [$id]
        )->fetch();
        return $row ? self::mapCompany($row) : null;
    }

    public static function findBySlug(string $slug): ?array
    {
        $row = db()->query(
            'SELECT * FROM companies WHERE slug = ? AND deleted_at IS NULL',
            [$slug]
        )->fetch();
        return $row ? self::mapCompany($row) : null;
    }

    public static function create(array $data): int
    {
        $data['slug'] = self::generateUniqueSlug($data['name']);
        $data['created_at'] = now();
        $data['updated_at'] = now();
        return db()->insert('companies', $data);
    }

    public static function update(int $id, array $data): void
    {
        $data['updated_at'] = now();
        db()->update('companies', $data, 'id = ?', [$id]);
    }

    public static function softDelete(int $id): void
    {
        db()->update('companies', ['deleted_at' => now(), 'updated_at' => now()], 'id = ?', [$id]);
    }

    public static function restore(int $id): void
    {
        db()->update('companies', ['deleted_at' => null, 'updated_at' => now()], 'id = ?', [$id]);
    }

    public static function count(array $filters = []): int
    {
        $sql = 'SELECT COUNT(*) as cnt FROM companies WHERE deleted_at IS NULL';
        $params = [];
        if (isset($filters['is_public'])) {
            $sql .= ' AND is_public = ?';
            $params[] = (int) $filters['is_public'];
        }
        return (int) db()->query($sql, $params)->fetch()['cnt'];
    }

    public static function generateUniqueSlug(string $name): string
    {
        $slug = slugify($name);
        $original = $slug;
        $counter = 1;

        while (self::slugExists($slug)) {
            $slug = $original . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    private static function slugExists(string $slug): bool
    {
        $result = db()->query('SELECT COUNT(*) as cnt FROM companies WHERE slug = ?', [$slug])->fetch();
        return $result['cnt'] > 0;
    }

    public static function getDeleted(): array
    {
        return db()->query(
            'SELECT * FROM companies WHERE deleted_at IS NOT NULL ORDER BY deleted_at DESC'
        )->fetchAll();
    }
}

<?php

namespace App\Models;

class BrandColor
{
    public static function findById(int $id): ?array
    {
        return db()->query('SELECT * FROM brand_colors WHERE id = ?', [$id])->fetch() ?: null;
    }

    public static function findByCompany(int $companyId): array
    {
        return db()->query(
            'SELECT * FROM brand_colors WHERE company_id = ? ORDER BY sort_order ASC, id ASC',
            [$companyId]
        )->fetchAll();
    }

    public static function create(array $data): int
    {
        $data['created_at'] = now();
        $data['updated_at'] = now();
        return db()->insert('brand_colors', $data);
    }

    public static function update(int $id, array $data): void
    {
        $data['updated_at'] = now();
        db()->update('brand_colors', $data, 'id = ?', [$id]);
    }

    public static function delete(int $id): void
    {
        db()->delete('brand_colors', 'id = ?', [$id]);
    }

    public static function countByCompany(int $companyId): int
    {
        return (int) db()->query(
            'SELECT COUNT(*) as cnt FROM brand_colors WHERE company_id = ?',
            [$companyId]
        )->fetch()['cnt'];
    }
}

<?php

namespace App\Models;

class BrandFont
{
    public static function findById(int $id): ?array
    {
        return db()->query('SELECT * FROM brand_fonts WHERE id = ?', [$id])->fetch() ?: null;
    }

    public static function findByCompany(int $companyId): array
    {
        return db()->query(
            'SELECT * FROM brand_fonts WHERE company_id = ? ORDER BY sort_order ASC, id ASC',
            [$companyId]
        )->fetchAll();
    }

    public static function create(array $data): int
    {
        $data['created_at'] = now();
        $data['updated_at'] = now();
        return db()->insert('brand_fonts', $data);
    }

    public static function update(int $id, array $data): void
    {
        $data['updated_at'] = now();
        db()->update('brand_fonts', $data, 'id = ?', [$id]);
    }

    public static function delete(int $id): void
    {
        db()->delete('brand_fonts', 'id = ?', [$id]);
    }

    public static function countByCompany(int $companyId): int
    {
        return (int) db()->query(
            'SELECT COUNT(*) as cnt FROM brand_fonts WHERE company_id = ?',
            [$companyId]
        )->fetch()['cnt'];
    }
}

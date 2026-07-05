<?php

namespace App\Models;

class Download
{
    public static function create(array $data): int
    {
        $data['created_at'] = now();
        return db()->insert('downloads', $data);
    }

    public static function countByCompany(int $companyId): int
    {
        return (int) db()->query(
            'SELECT COUNT(*) as cnt FROM downloads WHERE company_id = ?',
            [$companyId]
        )->fetch()['cnt'];
    }

    public static function countThisMonth(): int
    {
        $start = date('Y-m-01 00:00:00');
        return (int) db()->query(
            'SELECT COUNT(*) as cnt FROM downloads WHERE created_at >= ?',
            [$start]
        )->fetch()['cnt'];
    }

    public static function getMostDownloaded(int $limit = 10): array
    {
        return db()->query(
            'SELECT f.id, g.title as group_title, c.name as company_name, COUNT(d.id) as download_count
             FROM downloads d
             JOIN brand_asset_files f ON f.id = d.asset_file_id
             JOIN brand_asset_groups g ON g.id = f.asset_group_id
             JOIN companies c ON c.id = g.company_id
             GROUP BY f.id
             ORDER BY download_count DESC
             LIMIT ?',
            [$limit]
        )->fetchAll();
    }
}

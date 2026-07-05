<?php

namespace App\Models;

class BrandAssetGroup
{
    public static function findById(int $id): ?array
    {
        return db()->query(
            'SELECT * FROM brand_asset_groups WHERE id = ? AND deleted_at IS NULL',
            [$id]
        )->fetch() ?: null;
    }

    public static function findByCompany(int $companyId, ?string $assetType = null): array
    {
        $sql = 'SELECT * FROM brand_asset_groups WHERE company_id = ? AND deleted_at IS NULL';
        $params = [$companyId];

        if ($assetType) {
            $sql .= ' AND asset_type = ?';
            $params[] = $assetType;
        }

        $sql .= ' ORDER BY sort_order ASC, created_at DESC';
        return db()->query($sql, $params)->fetchAll();
    }

    public static function findBySlug(int $companyId, string $theme, string $slug): ?array
    {
        return db()->query(
            'SELECT * FROM brand_asset_groups WHERE company_id = ? AND theme = ? AND slug = ? AND deleted_at IS NULL',
            [$companyId, $theme, $slug]
        )->fetch() ?: null;
    }

    public static function create(array $data): int
    {
        $data['slug'] = slugify($data['title']);
        $data['created_at'] = now();
        $data['updated_at'] = now();
        return db()->insert('brand_asset_groups', $data);
    }

    public static function update(int $id, array $data): void
    {
        $data['updated_at'] = now();
        db()->update('brand_asset_groups', $data, 'id = ?', [$id]);
    }

    public static function softDelete(int $id): void
    {
        db()->update('brand_asset_groups', ['deleted_at' => now(), 'updated_at' => now()], 'id = ?', [$id]);
    }

    public static function restore(int $id): void
    {
        db()->update('brand_asset_groups', ['deleted_at' => null, 'updated_at' => now()], 'id = ?', [$id]);
    }

    public static function countByCompany(int $companyId, ?string $assetType = null): int
    {
        $sql = 'SELECT COUNT(*) as cnt FROM brand_asset_groups WHERE company_id = ? AND deleted_at IS NULL';
        $params = [$companyId];
        if ($assetType) {
            $sql .= ' AND asset_type = ?';
            $params[] = $assetType;
        }
        return (int) db()->query($sql, $params)->fetch()['cnt'];
    }

    public static function countAll(): int
    {
        return (int) db()->query('SELECT COUNT(*) as cnt FROM brand_asset_groups WHERE deleted_at IS NULL')->fetch()['cnt'];
    }

    public static function getWithFiles(int $id): ?array
    {
        $group = self::findById($id);
        if (!$group) return null;
        $group['files'] = BrandAssetFile::findByGroup($id);
        return $group;
    }

    public static function getCompanyGroupsWithPrimaryFile(int $companyId, ?string $assetType = null): array
    {
        $groups = self::findByCompany($companyId, $assetType);
        foreach ($groups as &$group) {
            $group['primary_file'] = BrandAssetFile::getPrimaryForGroup($group['id']);
            $group['file_count'] = BrandAssetFile::countByGroup($group['id']);
        }
        return $groups;
    }
}

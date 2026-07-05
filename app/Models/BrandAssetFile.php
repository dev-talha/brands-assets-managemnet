<?php

namespace App\Models;

class BrandAssetFile
{
    public static function findById(int $id): ?array
    {
        return db()->query(
            'SELECT * FROM brand_asset_files WHERE id = ? AND deleted_at IS NULL',
            [$id]
        )->fetch() ?: null;
    }

    public static function findByGroup(int $groupId): array
    {
        return db()->query(
            'SELECT * FROM brand_asset_files WHERE asset_group_id = ? AND deleted_at IS NULL ORDER BY is_primary DESC, extension ASC',
            [$groupId]
        )->fetchAll();
    }

    public static function findByToken(string $token): ?array
    {
        return db()->query(
            'SELECT * FROM brand_asset_files WHERE public_token = ? AND deleted_at IS NULL',
            [$token]
        )->fetch() ?: null;
    }

    public static function findByCdnPath(string $cdnPath): ?array
    {
        return db()->query(
            'SELECT * FROM brand_asset_files WHERE cdn_path = ? AND deleted_at IS NULL',
            [$cdnPath]
        )->fetch() ?: null;
    }

    public static function findByCacheVersion(string $version, string $slug, string $ext): ?array
    {
        return db()->query(
            'SELECT f.* FROM brand_asset_files f
             JOIN brand_asset_groups g ON g.id = f.asset_group_id
             WHERE f.cache_version = ? AND g.slug = ? AND f.extension = ? AND f.deleted_at IS NULL',
            [$version, $slug, $ext]
        )->fetch() ?: null;
    }

    public static function findForCdn(int $companyId, string $theme, string $assetSlug, string $ext): ?array
    {
        return db()->query(
            'SELECT f.*, g.slug as group_slug, g.theme as group_theme
             FROM brand_asset_files f
             JOIN brand_asset_groups g ON g.id = f.asset_group_id
             WHERE g.company_id = ? AND g.theme = ? AND g.slug = ?
             AND f.extension = ? AND f.is_public = 1 AND f.is_cdn_enabled = 1
             AND g.is_public = 1 AND g.status = \'approved\'
             AND f.deleted_at IS NULL AND g.deleted_at IS NULL
             ORDER BY f.is_primary DESC, f.created_at DESC
             LIMIT 1',
            [$companyId, $theme, $assetSlug, $ext]
        )->fetch() ?: null;
    }

    public static function create(array $data): int
    {
        $data['created_at'] = now();
        $data['updated_at'] = now();
        return db()->insert('brand_asset_files', $data);
    }

    public static function update(int $id, array $data): void
    {
        $data['updated_at'] = now();
        db()->update('brand_asset_files', $data, 'id = ?', [$id]);
    }

    public static function softDelete(int $id): void
    {
        $file = db()->query('SELECT cdn_path, public_token FROM brand_asset_files WHERE id = ?', [$id])->fetch();
        if ($file) {
            db()->update('brand_asset_files', [
                'deleted_at' => now(),
                'updated_at' => now(),
                'cdn_path' => $file['cdn_path'] . '_del_' . time(),
                'public_token' => $file['public_token'] . '_del_' . time()
            ], 'id = ?', [$id]);
        }
    }

    public static function restore(int $id): void
    {
        db()->update('brand_asset_files', ['deleted_at' => null, 'updated_at' => now()], 'id = ?', [$id]);
    }

    public static function getPrimaryForGroup(int $groupId): ?array
    {
        return db()->query(
            'SELECT * FROM brand_asset_files WHERE asset_group_id = ? AND deleted_at IS NULL ORDER BY is_primary DESC, created_at ASC LIMIT 1',
            [$groupId]
        )->fetch() ?: null;
    }

    public static function countByGroup(int $groupId): int
    {
        return (int) db()->query(
            'SELECT COUNT(*) as cnt FROM brand_asset_files WHERE asset_group_id = ? AND deleted_at IS NULL',
            [$groupId]
        )->fetch()['cnt'];
    }

    public static function countAll(): int
    {
        return (int) db()->query('SELECT COUNT(*) as cnt FROM brand_asset_files WHERE deleted_at IS NULL')->fetch()['cnt'];
    }

    public static function countPublicCdn(): int
    {
        return (int) db()->query(
            'SELECT COUNT(*) as cnt FROM brand_asset_files WHERE is_public = 1 AND is_cdn_enabled = 1 AND deleted_at IS NULL'
        )->fetch()['cnt'];
    }

    public static function getAllForCompany(int $companyId): array
    {
        return db()->query(
            'SELECT f.*, g.title as group_title, g.slug as group_slug, g.theme, g.asset_type
             FROM brand_asset_files f
             JOIN brand_asset_groups g ON g.id = f.asset_group_id
             WHERE g.company_id = ? AND f.deleted_at IS NULL AND g.deleted_at IS NULL
             ORDER BY g.sort_order ASC, g.title ASC, f.extension ASC',
            [$companyId]
        )->fetchAll();
    }

    public static function getRecentUploads(int $limit = 10): array
    {
        return db()->query(
            'SELECT f.*, g.title as group_title, c.name as company_name, c.slug as company_slug
             FROM brand_asset_files f
             JOIN brand_asset_groups g ON g.id = f.asset_group_id
             JOIN companies c ON c.id = g.company_id
             WHERE f.deleted_at IS NULL
             ORDER BY f.created_at DESC
             LIMIT ?',
            [$limit]
        )->fetchAll();
    }

    public static function getTotalStorageBytes(): int
    {
        $result = db()->query('SELECT SUM(file_size) as total FROM brand_asset_files WHERE deleted_at IS NULL')->fetch();
        return (int) ($result['total'] ?? 0);
    }
}

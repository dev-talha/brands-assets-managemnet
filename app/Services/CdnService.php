<?php

namespace App\Services;

use App\Models\BrandAssetFile;
use App\Models\BrandAssetGroup;
use App\Models\Company;
use App\Core\Response;

class CdnService
{
    /**
     * Serve a file via the latest human-readable CDN URL.
     */
    public static function serveLatest(string $companySlug, string $theme, string $assetSlug, string $ext): void
    {
        // Find company
        $company = Company::findBySlug($companySlug);
        if (!$company || !$company['is_public'] || $company['status'] !== 'active') {
            Response::error(404, 'Not found');
        }

        // Find asset file
        $file = BrandAssetFile::findForCdn($company['id'], $theme, $assetSlug, $ext);
        if (!$file) {
            Response::error(404, 'Asset not found');
        }

        self::checkAccessLimits($file);

        // Log access
        self::logAccess($file['id']);

        // Stream file
        $fullPath = storage_path('uploads/' . $file['storage_path']);
        $mimeType = getMimeTypeForExtension($ext);

        $cleanFilename = preg_replace('/^' . preg_quote($company['slug'], '/') . '-/', '', basename($file['original_filename']));
        $streamName = $company['slug'] . '-' . $cleanFilename;

        Response::stream($fullPath, $streamName, $mimeType, [
            'Cache-Control: public, max-age=3600',
            'Access-Control-Allow-Origin: *'
        ]);
    }

    /**
     * Serve a file via the token-based CDN URL.
     */
    public static function serveByToken(string $token, string $ext): void
    {
        $file = BrandAssetFile::findByToken($token);
        if (!$file || $file['extension'] !== $ext) {
            Response::error(404, 'Not found');
        }
        if (!$file['is_public'] || !$file['is_cdn_enabled']) {
            Response::error(403, 'Access denied');
        }

        // Verify parent group and company
        $group = BrandAssetGroup::findById($file['asset_group_id']);
        if (!$group || !$group['is_public'] || $group['status'] !== 'approved') {
            Response::error(404, 'Not found');
        }

        $company = Company::findById($group['company_id']);
        if (!$company || !$company['is_public'] || $company['status'] !== 'active') {
            Response::error(404, 'Not found');
        }

        self::checkAccessLimits($file);

        self::logAccess($file['id']);

        $fullPath = storage_path('uploads/' . $file['storage_path']);
        $mimeType = getMimeTypeForExtension($ext);

        $cleanFilename = preg_replace('/^' . preg_quote($company['slug'], '/') . '-/', '', basename($file['original_filename']));
        $streamName = $company['slug'] . '-' . $cleanFilename;

        Response::stream($fullPath, $streamName, $mimeType, [
            'Cache-Control: public, max-age=3600',
            'Access-Control-Allow-Origin: *'
        ]);
    }

    /**
     * Serve a file via the versioned immutable CDN URL.
     */
    public static function serveVersioned(string $version, string $assetSlug, string $ext): void
    {
        $file = BrandAssetFile::findByCacheVersion($version, $assetSlug, $ext);
        if (!$file) {
            Response::error(404, 'Not found');
        }

        self::checkAccessLimits($file);

        self::logAccess($file['id']);

        $fullPath = storage_path('uploads/' . $file['storage_path']);
        $mimeType = getMimeTypeForExtension($ext);

        $group = BrandAssetGroup::findById($file['asset_group_id']);
        $company = $group ? Company::findById($group['company_id']) : null;
        
        if ($company) {
            $cleanFilename = preg_replace('/^' . preg_quote($company['slug'], '/') . '-/', '', basename($file['original_filename']));
            $streamName = $company['slug'] . '-' . $cleanFilename;
        } else {
            $streamName = "{$assetSlug}.{$ext}";
        }

        Response::stream($fullPath, $streamName, $mimeType, [
            'Cache-Control: public, max-age=31536000, immutable',
            'Access-Control-Allow-Origin: *'
        ]);
    }

    /**
     * Generate all CDN URLs for a file.
     */
    public static function generateUrls(array $file, array $group, array $company): array
    {
        $baseUrl = rtrim(env('CDN_BASE_URL', env('APP_URL', 'http://localhost:8000')), '/');

        return [
            'latest' => "{$baseUrl}/cdn/{$company['slug']}/{$group['theme']}/{$group['slug']}/{$file['extension']}",
            'token' => "{$baseUrl}/cdn/file/{$file['public_token']}/{$file['extension']}",
            'versioned' => "{$baseUrl}/cdn/v/{$file['cache_version']}/{$group['slug']}/{$file['extension']}",
        ];
    }

    /**
     * Generate HTML embed code.
     */
    public static function generateEmbed(array $file, array $group, array $company): string
    {
        $urls = self::generateUrls($file, $group, $company);
        return '<img src="' . $urls['latest'] . '" alt="' . htmlspecialchars($company['name'] . ' - ' . $group['title']) . '">';
    }

    /**
     * Check global and file-specific access limits.
     */
    private static function checkAccessLimits(array $file): void
    {
        // Check if public pages and CDN are disabled globally
        if (settings('public_brand_pages') === '0') {
            Response::error(403, 'CDN access is currently disabled.');
        }

        // Check CDN expiration
        $expirationDays = (int) settings('cdn_expiration_days');
        if ($expirationDays > 0) {
            $updatedAt = strtotime($file['updated_at']);
            $expiresAt = $updatedAt + ($expirationDays * 86400);
            if (time() > $expiresAt) {
                Response::error(410, 'This CDN link has expired.');
            }
        }

        // Check CDN hit limit
        $accessLimit = (int) settings('cdn_access_limit');
        if ($accessLimit > 0) {
            $hits = self::getHitCount((int) $file['id']);
            if ($hits >= $accessLimit) {
                Response::error(429, 'This CDN link has exceeded its maximum access limit.');
            }
        }
    }

    /**
     * Log CDN access.
     */
    private static function logAccess(int $fileId): void
    {
        try {
            db()->insert('cdn_access_logs', [
                'asset_file_id' => $fileId,
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '',
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
                'referer' => $_SERVER['HTTP_REFERER'] ?? '',
                'accessed_at' => now(),
            ]);
        } catch (\Throwable $e) {
            // Don't break CDN serving if logging fails
        }
    }

    /**
     * Get CDN hit count for a file.
     */
    public static function getHitCount(int $fileId): int
    {
        return (int) db()->query(
            'SELECT COUNT(*) as cnt FROM cdn_access_logs WHERE asset_file_id = ?',
            [$fileId]
        )->fetch()['cnt'];
    }

    /**
     * Get total CDN hits this month.
     */
    public static function getMonthlyHits(): int
    {
        $start = date('Y-m-01 00:00:00');
        return (int) db()->query(
            'SELECT COUNT(*) as cnt FROM cdn_access_logs WHERE accessed_at >= ?',
            [$start]
        )->fetch()['cnt'];
    }
}

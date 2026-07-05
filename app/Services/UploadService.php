<?php

namespace App\Services;

use App\Models\BrandAssetGroup;
use App\Models\BrandAssetFile;
use App\Models\Company;

class UploadService
{
    /**
     * Handle asset file upload.
     */
    public static function handleUpload(
        array $file,
        int $companyId,
        string $title,
        string $assetType,
        string $theme = 'default',
        bool $isPublic = true,
        ?string $description = null
    ): array {
        // Validate file
        $maxMb = (int) env('UPLOAD_MAX_MB', '50');
        $errors = validateUploadedFile($file, $maxMb);
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        $company = Company::findById($companyId);
        if (!$company) {
            return ['success' => false, 'errors' => ['Company not found.']];
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $originalFilename = $file['name'];
        $storedFilename = generateToken(16) . '.' . $ext;
        $year = date('Y');
        $month = date('m');

        // Create storage directory
        $storageDir = storage_path("uploads/{$company['slug']}/{$year}/{$month}");
        if (!is_dir($storageDir)) {
            mkdir($storageDir, 0755, true);
        }

        $storagePath = "{$company['slug']}/{$year}/{$month}/{$storedFilename}";
        $fullPath = storage_path("uploads/{$storagePath}");

        // Move uploaded file
        if (!move_uploaded_file($file['tmp_name'], $fullPath)) {
            return ['success' => false, 'errors' => ['Failed to save file.']];
        }

        // SVG sanitization
        if ($ext === 'svg') {
            $svgContent = file_get_contents($fullPath);
            $sanitized = sanitizeSvg($svgContent);
            file_put_contents($fullPath, $sanitized);
        }

        // Get image dimensions
        $dimensions = getImageDimensions($fullPath, $ext);

        // Find or create asset group
        $slug = slugify($title);
        $group = BrandAssetGroup::findBySlug($companyId, $theme, $slug);

        if (!$group) {
            $groupId = BrandAssetGroup::create([
                'company_id' => $companyId,
                'title' => $title,
                'slug' => $slug,
                'asset_type' => $assetType,
                'theme' => $theme,
                'description' => $description,
                'is_public' => $isPublic ? 1 : 0,
            ]);
        } else {
            $groupId = $group['id'];
        }

        // Generate CDN path and token
        $publicToken = generateToken(10);
        $cdnPath = "{$company['slug']}/{$theme}/{$slug}.{$ext}";
        $cacheVersion = date('Y-m-d') . '-' . substr($publicToken, 0, 6);

        // Check if CDN path already exists (same format uploaded again)
        $existingFile = BrandAssetFile::findByCdnPath($cdnPath);
        if ($existingFile) {
            // This is a replacement — soft delete the old file
            BrandAssetFile::softDelete($existingFile['id']);
        }

        // Check if this is the first file in the group
        $fileCount = BrandAssetFile::countByGroup($groupId);
        $isPrimary = ($fileCount === 0) ? 1 : 0;

        // Create asset file record
        $fileId = BrandAssetFile::create([
            'asset_group_id' => $groupId,
            'original_filename' => $originalFilename,
            'stored_filename' => $storedFilename,
            'storage_path' => $storagePath,
            'public_token' => $publicToken,
            'extension' => $ext,
            'mime_type' => getMimeTypeForExtension($ext),
            'file_size' => filesize($fullPath),
            'width' => $dimensions['width'] ?? null,
            'height' => $dimensions['height'] ?? null,
            'cdn_path' => $cdnPath,
            'cache_version' => $cacheVersion,
            'is_primary' => $isPrimary,
            'is_public' => $isPublic ? 1 : 0,
            'is_cdn_enabled' => 1,
            'uploaded_by' => currentUser()['id'] ?? null,
        ]);

        // Generate thumbnail for raster images
        if (in_array($ext, ['png', 'jpg', 'jpeg', 'webp', 'gif'])) {
            ThumbnailService::generate($fullPath, $storedFilename, $ext);
        }

        return [
            'success' => true,
            'file_id' => $fileId,
            'group_id' => $groupId,
            'cdn_path' => $cdnPath,
            'public_token' => $publicToken,
        ];
    }

    /**
     * Replace an existing asset file while keeping the same CDN path.
     */
    public static function replaceFile(int $fileId, array $uploadedFile): array
    {
        $existing = BrandAssetFile::findById($fileId);
        if (!$existing) {
            return ['success' => false, 'errors' => ['File not found.']];
        }

        $ext = strtolower(pathinfo($uploadedFile['name'], PATHINFO_EXTENSION));
        if ($ext !== $existing['extension']) {
            return ['success' => false, 'errors' => ['Replacement file must have the same extension (.'. $existing['extension'] .')']];
        }

        $errors = validateUploadedFile($uploadedFile);
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        // Get old file path
        $oldPath = storage_path('uploads/' . $existing['storage_path']);

        // Generate new stored filename
        $storedFilename = generateToken(16) . '.' . $ext;
        $dirPath = dirname($existing['storage_path']);
        $newStoragePath = $dirPath . '/' . $storedFilename;
        $fullPath = storage_path('uploads/' . $newStoragePath);

        // Move new file
        if (!move_uploaded_file($uploadedFile['tmp_name'], $fullPath)) {
            return ['success' => false, 'errors' => ['Failed to save file.']];
        }

        // SVG sanitization
        if ($ext === 'svg') {
            $svgContent = file_get_contents($fullPath);
            file_put_contents($fullPath, sanitizeSvg($svgContent));
        }

        // Get dimensions
        $dimensions = getImageDimensions($fullPath, $ext);

        // Generate new cache version
        $cacheVersion = date('Y-m-d') . '-' . generateToken(3);

        // Update file record — CDN path stays the same!
        BrandAssetFile::update($fileId, [
            'original_filename' => $uploadedFile['name'],
            'stored_filename' => $storedFilename,
            'storage_path' => $newStoragePath,
            'file_size' => filesize($fullPath),
            'width' => $dimensions['width'] ?? null,
            'height' => $dimensions['height'] ?? null,
            'cache_version' => $cacheVersion,
        ]);

        // Delete old file
        if (file_exists($oldPath)) {
            unlink($oldPath);
        }

        // Regenerate thumbnail
        if (in_array($ext, ['png', 'jpg', 'jpeg', 'webp', 'gif'])) {
            ThumbnailService::generate($fullPath, $storedFilename, $ext);
        }

        return ['success' => true, 'file_id' => $fileId];
    }
}

<?php

namespace App\Services;

class BackupService
{
    /**
     * Create a database backup.
     */
    public static function backupDatabase(): ?string
    {
        $backupDir = storage_path('backups');
        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }

        $timestamp = date('Y-m-d-His');
        $filename = "backup-db-{$timestamp}.sqlite";
        $source = base_path(env('DB_DATABASE', 'database/database.sqlite'));
        $dest = $backupDir . '/' . $filename;

        if (!file_exists($source)) {
            return null;
        }

        copy($source, $dest);
        return $filename;
    }

    /**
     * Create a backup of uploads directory as ZIP.
     */
    public static function backupUploads(): ?string
    {
        if (!class_exists('ZipArchive')) {
            return null;
        }

        $backupDir = storage_path('backups');
        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }

        $timestamp = date('Y-m-d-His');
        $filename = "backup-uploads-{$timestamp}.zip";
        $zipPath = $backupDir . '/' . $filename;
        $uploadsDir = storage_path('uploads');

        if (!is_dir($uploadsDir)) {
            return null;
        }

        $zip = new \ZipArchive();
        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            return null;
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($uploadsDir, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($iterator as $file) {
            if (!$file->isDir()) {
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen($uploadsDir) + 1);
                $zip->addFile($filePath, $relativePath);
            }
        }

        $zip->close();
        return $filename;
    }

    /**
     * List existing backups.
     */
    public static function listBackups(): array
    {
        $backupDir = storage_path('backups');
        if (!is_dir($backupDir)) return [];

        $files = glob($backupDir . '/backup-*');
        $backups = [];
        foreach ($files as $file) {
            $backups[] = [
                'name' => basename($file),
                'size' => formatBytes(filesize($file)),
                'created' => date('Y-m-d H:i:s', filemtime($file)),
            ];
        }
        usort($backups, fn($a, $b) => strcmp($b['created'], $a['created']));
        return $backups;
    }
}

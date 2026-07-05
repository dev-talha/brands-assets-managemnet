<?php

namespace App\Services;

class ZipService
{
    /**
     * Create a ZIP file from an array of files.
     * @param array $files Array of ['path' => string, 'name' => string]
     * @param string $zipName Output ZIP filename
     * @return string|null Path to the created ZIP file
     */
    public static function createZip(array $files, string $zipName): ?string
    {
        if (!class_exists('ZipArchive')) {
            return null;
        }

        $tempDir = storage_path('temp');
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $zipPath = $tempDir . '/' . $zipName;
        $zip = new \ZipArchive();

        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            return null;
        }

        foreach ($files as $file) {
            if (file_exists($file['path'])) {
                $zip->addFile($file['path'], $file['name']);
            }
        }

        $zip->close();
        return $zipPath;
    }

    /**
     * Send ZIP file to browser and delete temp file.
     */
    public static function sendAndCleanup(string $zipPath, string $downloadName): void
    {
        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="' . $downloadName . '"');
        header('Content-Length: ' . filesize($zipPath));
        header('Cache-Control: no-cache');

        readfile($zipPath);

        // Cleanup
        unlink($zipPath);
        exit;
    }
}

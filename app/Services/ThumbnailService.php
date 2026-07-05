<?php

namespace App\Services;

class ThumbnailService
{
    public const THUMB_WIDTH = 400;
    public const THUMB_HEIGHT = 300;

    public static function generate(string $sourcePath, string $filename, string $ext): ?string
    {
        $thumbDir = storage_path('thumbnails');
        if (!is_dir($thumbDir)) {
            mkdir($thumbDir, 0755, true);
        }

        $thumbPath = $thumbDir . '/' . $filename;

        if (!function_exists('imagecreatetruecolor')) {
            // GD not available, skip thumbnail
            return null;
        }

        try {
            $source = match ($ext) {
                'jpg', 'jpeg' => @imagecreatefromjpeg($sourcePath),
                'png' => @imagecreatefrompng($sourcePath),
                'webp' => @imagecreatefromwebp($sourcePath),
                'gif' => @imagecreatefromgif($sourcePath),
                default => null,
            };

            if (!$source) return null;

            $origW = imagesx($source);
            $origH = imagesy($source);

            // Calculate proportional size
            $ratio = min(self::THUMB_WIDTH / $origW, self::THUMB_HEIGHT / $origH);
            $newW = (int) ($origW * $ratio);
            $newH = (int) ($origH * $ratio);

            $thumb = imagecreatetruecolor($newW, $newH);

            // Handle transparency for PNG/WEBP
            if (in_array($ext, ['png', 'webp'])) {
                imagealphablending($thumb, false);
                imagesavealpha($thumb, true);
                $transparent = imagecolorallocatealpha($thumb, 0, 0, 0, 127);
                imagefilledrectangle($thumb, 0, 0, $newW, $newH, $transparent);
            }

            imagecopyresampled($thumb, $source, 0, 0, 0, 0, $newW, $newH, $origW, $origH);

            match ($ext) {
                'jpg', 'jpeg' => imagejpeg($thumb, $thumbPath, 85),
                'png' => imagepng($thumb, $thumbPath, 6),
                'webp' => imagewebp($thumb, $thumbPath, 85),
                'gif' => imagegif($thumb, $thumbPath),
            };

            imagedestroy($source);
            imagedestroy($thumb);

            return $thumbPath;
        } catch (\Throwable $e) {
            return null;
        }
    }

    public static function getPath(string $filename): ?string
    {
        $path = storage_path('thumbnails/' . $filename);
        return file_exists($path) ? $path : null;
    }

    public static function exists(string $filename): bool
    {
        return file_exists(storage_path('thumbnails/' . $filename));
    }
}

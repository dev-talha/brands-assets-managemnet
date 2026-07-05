<?php
/**
 * Validation helpers for file uploads.
 */

// Extensions allowed for CDN serving
const CDN_IMAGE_EXTENSIONS = ['svg', 'png', 'jpg', 'jpeg', 'webp', 'gif', 'avif'];

// Extensions allowed for download-only files
const DOWNLOAD_EXTENSIONS = ['pdf', 'ai', 'eps', 'zip', 'psd'];

// All allowed extensions
const ALLOWED_EXTENSIONS = ['svg', 'png', 'jpg', 'jpeg', 'webp', 'gif', 'avif', 'pdf', 'ai', 'eps', 'zip', 'psd'];

// Blocked dangerous extensions
const BLOCKED_EXTENSIONS = [
    'php', 'phtml', 'php3', 'php4', 'php5', 'phar',
    'exe', 'sh', 'bash', 'bat', 'cmd',
    'js', 'html', 'htm', 'iframe',
    'htaccess', 'ini', 'pl', 'py', 'rb', 'jar'
];

// MIME type mapping
const MIME_TYPE_MAP = [
    'svg'  => 'image/svg+xml',
    'png'  => 'image/png',
    'jpg'  => 'image/jpeg',
    'jpeg' => 'image/jpeg',
    'webp' => 'image/webp',
    'gif'  => 'image/gif',
    'avif' => 'image/avif',
    'pdf'  => 'application/pdf',
    'zip'  => 'application/zip',
    'ai'   => 'application/postscript',
    'eps'  => 'application/postscript',
    'psd'  => 'application/octet-stream',
];

// Allowed MIME types per extension
const ALLOWED_MIMES = [
    'svg'  => ['image/svg+xml', 'text/xml', 'application/xml', 'text/plain'],
    'png'  => ['image/png'],
    'jpg'  => ['image/jpeg'],
    'jpeg' => ['image/jpeg'],
    'webp' => ['image/webp'],
    'gif'  => ['image/gif'],
    'avif' => ['image/avif'],
    'pdf'  => ['application/pdf'],
    'zip'  => ['application/zip', 'application/x-zip-compressed'],
    'ai'   => ['application/postscript', 'application/pdf', 'application/octet-stream'],
    'eps'  => ['application/postscript', 'application/octet-stream'],
    'psd'  => ['application/octet-stream', 'image/vnd.adobe.photoshop'],
];

function validateUploadedFile(array $file, int $maxSizeMb = 50): array
{
    $errors = [];

    // Check upload error
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errors[] = 'File upload failed with error code: ' . $file['error'];
        return $errors;
    }

    // Check file size
    $maxBytes = $maxSizeMb * 1024 * 1024;
    if ($file['size'] > $maxBytes) {
        $errors[] = "File size exceeds maximum of {$maxSizeMb}MB";
    }

    // Extract and validate extension
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (in_array($ext, BLOCKED_EXTENSIONS)) {
        $errors[] = "File type .{$ext} is not allowed for security reasons";
        return $errors;
    }

    if (!in_array($ext, ALLOWED_EXTENSIONS)) {
        $errors[] = "File type .{$ext} is not supported";
        return $errors;
    }

    // Validate MIME type using finfo
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $detectedMime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (isset(ALLOWED_MIMES[$ext])) {
        if (!in_array($detectedMime, ALLOWED_MIMES[$ext])) {
            $errors[] = "File content does not match the expected type for .{$ext} (detected: {$detectedMime})";
        }
    }

    return $errors;
}

function getImageDimensions(string $filePath, string $ext): ?array
{
    if (in_array($ext, ['svg', 'ai', 'eps', 'psd', 'zip', 'pdf'])) {
        return null;
    }
    $info = @getimagesize($filePath);
    if ($info) {
        return ['width' => $info[0], 'height' => $info[1]];
    }
    return null;
}

function getMimeTypeForExtension(string $ext): string
{
    return MIME_TYPE_MAP[strtolower($ext)] ?? 'application/octet-stream';
}

function isImageExtension(string $ext): bool
{
    return in_array(strtolower($ext), CDN_IMAGE_EXTENSIONS);
}

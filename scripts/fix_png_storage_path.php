<?php
/**
 * Fix storage_path in brand_asset_files — remove the "uploads/" prefix
 * that was incorrectly added during PNG seeding.
 *
 * Run: php scripts/fix_png_storage_path.php
 */

define('BASE_PATH', dirname(__DIR__));

$envFile = BASE_PATH . '/.env';
if (file_exists($envFile)) {
    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (!strpos($line, '=')) continue;
        [$key, $val] = explode('=', $line, 2);
        $_ENV[trim($key)] = trim($val, '"\'');
        putenv(trim($key) . '=' . trim($val, '"\''));
    }
}

$dbPath = BASE_PATH . '/' . ($_ENV['DB_DATABASE'] ?? 'database/database.sqlite');
$pdo = new PDO('sqlite:' . $dbPath);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

$now = date('Y-m-d H:i:s');
$fixed = 0;

// Find all PNG files seeded with wrong "uploads/" prefix in storage_path
$files = $pdo->query(
    "SELECT id, storage_path, cdn_path, original_filename 
     FROM brand_asset_files 
     WHERE extension = 'png' 
       AND storage_path LIKE 'uploads/%'
       AND deleted_at IS NULL"
)->fetchAll();

foreach ($files as $file) {
    // Remove leading "uploads/" prefix
    $correctPath    = preg_replace('#^uploads/#', '', $file['storage_path']);
    $correctCdnPath = preg_replace('#^uploads/#', '', $file['cdn_path']);

    // Verify the file actually exists at the corrected path
    $realPath = BASE_PATH . '/storage/uploads/' . $correctPath;
    if (!file_exists($realPath)) {
        echo "⚠️  File not found at: {$realPath}\n";
        continue;
    }

    // Get image dimensions while we're at it
    $imageInfo = @getimagesize($realPath);
    $width  = $imageInfo ? $imageInfo[0] : null;
    $height = $imageInfo ? $imageInfo[1] : null;

    $stmt = $pdo->prepare(
        "UPDATE brand_asset_files 
         SET storage_path = ?, cdn_path = ?, width = ?, height = ?, updated_at = ?
         WHERE id = ?"
    );
    $stmt->execute([$correctPath, $correctCdnPath, $width, $height, $now, $file['id']]);

    echo "✅ Fixed id={$file['id']}: {$file['storage_path']} → {$correctPath} [{$width}x{$height}]\n";
    $fixed++;
}

echo "\n📊 Fixed: {$fixed} records\n";

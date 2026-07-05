<?php
/**
 * Logo Asset Seeder
 * -------------------------------------------------------
 * প্রতিটি company-র avatar logo কে brand_asset_groups এবং
 * brand_asset_files table-এ properly insert করে।
 * CDN URL, token, cache_version সব generate হবে।
 *
 * Run: php scripts/seed_logo_assets.php
 */

define('BASE_PATH', dirname(__DIR__));

// Load env
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

require_once BASE_PATH . '/vendor/autoload.php';
require_once BASE_PATH . '/app/Support/helpers.php';

// Connect to SQLite
$dbPath = BASE_PATH . '/' . ($_ENV['DB_DATABASE'] ?? 'database/database.sqlite');
$pdo = new PDO('sqlite:' . $dbPath);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

$now = date('Y-m-d H:i:s');

// MIME types
$mimeMap = [
    'svg'  => 'image/svg+xml',
    'png'  => 'image/png',
    'jpg'  => 'image/jpeg',
    'jpeg' => 'image/jpeg',
    'webp' => 'image/webp',
];

function slugifyLocal(string $text): string {
    $text = strtolower($text);
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    $text = preg_replace('/[\s-]+/', '-', $text);
    return trim($text, '-');
}

function generateToken(int $length = 10): string {
    return bin2hex(random_bytes($length));
}

// -------------------------------------------------------
// Fetch all companies that have an avatar_image_path
// -------------------------------------------------------
$companies = $pdo->query(
    "SELECT id, name, slug, avatar_image_path FROM companies
     WHERE avatar_image_path IS NOT NULL AND avatar_image_path != '' AND status = 'active'
     ORDER BY id ASC"
)->fetchAll();

echo "\n=== Logo Asset Seeder ===\n";
echo "Companies with avatar: " . count($companies) . "\n\n";

$groupInserted = 0;
$fileInserted  = 0;
$skipped       = 0;

foreach ($companies as $company) {
    $companyId   = (int) $company['id'];
    $companyName = $company['name'];
    $companySlug = $company['slug'];
    $avatarPath  = $company['avatar_image_path']; // e.g. "companies/avatar/alpha-net.svg"

    echo "[{$companyId}] {$companyName} ({$companySlug})\n";

    // ---- Full path to the file ----
    $fullPath = BASE_PATH . '/storage/uploads/' . $avatarPath;
    if (!file_exists($fullPath)) {
        echo "     ✗ File not found: {$fullPath}\n\n";
        $skipped++;
        continue;
    }

    $ext      = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
    $mimeType = $mimeMap[$ext] ?? 'application/octet-stream';
    $fileSize = filesize($fullPath);
    $filename = basename($fullPath);

    // ---- Check if asset group already exists ----
    $existingGroup = $pdo->prepare(
        "SELECT id FROM brand_asset_groups WHERE company_id = ? AND theme = 'default' AND slug = 'logo'"
    );
    $existingGroup->execute([$companyId]);
    $group = $existingGroup->fetch();

    if ($group) {
        $groupId = (int) $group['id'];
        echo "     [GROUP EXISTS] id={$groupId}\n";
    } else {
        // Create brand_asset_group
        $stmt = $pdo->prepare(
            "INSERT INTO brand_asset_groups
                (company_id, title, slug, asset_type, theme, description, tags, is_public, status, sort_order, created_at, updated_at)
             VALUES
                (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $companyId,
            'Logo',                  // title
            'logo',                  // slug
            'logo',                  // asset_type
            'default',               // theme
            $companyName . ' official logo.',
            'logo,brand,identity',   // tags
            1,                       // is_public
            'approved',              // status
            0,                       // sort_order
            $now,
            $now,
        ]);
        $groupId = (int) $pdo->lastInsertId();
        echo "     ✓ Group created: id={$groupId}\n";
        $groupInserted++;
    }

    // ---- Check if file already exists in this group ----
    $existingFile = $pdo->prepare(
        "SELECT id FROM brand_asset_files WHERE asset_group_id = ? AND extension = ? AND deleted_at IS NULL"
    );
    $existingFile->execute([$groupId, $ext]);
    if ($existingFile->fetch()) {
        echo "     [FILE EXISTS] extension={$ext}, skipping\n\n";
        $skipped++;
        continue;
    }

    // ---- Move/copy file to company-specific folder ----
    $companyUploadsDir = BASE_PATH . '/storage/uploads/' . $companySlug;
    if (!is_dir($companyUploadsDir)) {
        mkdir($companyUploadsDir, 0755, true);
    }

    $storedFilename = 'logo.' . $ext;
    $destPath       = $companyUploadsDir . '/' . $storedFilename;

    // Copy avatar to brand's own folder (keep original too)
    if (!file_exists($destPath)) {
        copy($fullPath, $destPath);
    }

    $storagePath   = $companySlug . '/' . $storedFilename;
    $publicToken   = generateToken(16);  // 32 char hex
    $cacheVersion  = substr(md5($companySlug . $ext . time()), 0, 12);
    $cdnPath       = $companySlug . '/default/logo.' . $ext;

    // ---- Insert brand_asset_file ----
    $stmt = $pdo->prepare(
        "INSERT INTO brand_asset_files
            (asset_group_id, original_filename, stored_filename, storage_path,
             public_token, extension, mime_type, file_size,
             cdn_path, cache_version,
             is_primary, is_public, is_cdn_enabled,
             uploaded_by, created_at, updated_at)
         VALUES
            (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
    );
    $stmt->execute([
        $groupId,
        $filename,          // original_filename
        $storedFilename,    // stored_filename
        $storagePath,       // storage_path  e.g. alpha-net/logo.svg
        $publicToken,
        $ext,
        $mimeType,
        $fileSize,
        $cdnPath,           // cdn_path  e.g. alpha-net/default/logo.svg
        $cacheVersion,
        1,                  // is_primary
        1,                  // is_public
        1,                  // is_cdn_enabled
        null,               // uploaded_by (seeder)
        $now,
        $now,
    ]);

    $fileId = (int) $pdo->lastInsertId();
    echo "     ✓ File inserted: id={$fileId}\n";
    echo "     ✓ CDN path: cdn/{$companySlug}/default/logo.{$ext}\n";
    echo "     ✓ Token URL: cdn/file/{$publicToken}.{$ext}\n";
    $fileInserted++;

    echo "\n";
}

echo "=== Done! ===\n";
echo "Groups inserted : {$groupInserted}\n";
echo "Files inserted  : {$fileInserted}\n";
echo "Skipped         : {$skipped}\n";

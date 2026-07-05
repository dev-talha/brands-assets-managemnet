<?php
/**
 * Seed PNG logos into brand_asset_groups + brand_asset_files
 * Run: php scripts/seed_png_logos.php
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

$uploadsDir = BASE_PATH . '/storage/uploads';
$now = date('Y-m-d H:i:s');

$companies = $pdo->query(
    "SELECT id, name, slug FROM companies WHERE deleted_at IS NULL ORDER BY name ASC"
)->fetchAll();

$groupsCreated = 0;
$filesAdded    = 0;
$skipped       = 0;

foreach ($companies as $company) {
    $slug    = $company['slug'];
    $compId  = $company['id'];
    $pngPath = "{$uploadsDir}/{$slug}/logo.png";

    if (!file_exists($pngPath)) {
        echo "⏭  No PNG: {$slug}\n";
        $skipped++;
        continue;
    }

    $fileSize = filesize($pngPath);

    // Find or create logo group
    $stmt = $pdo->prepare(
        "SELECT id FROM brand_asset_groups 
         WHERE company_id = ? AND asset_type = 'logo' AND deleted_at IS NULL LIMIT 1"
    );
    $stmt->execute([$compId]);
    $group = $stmt->fetch();

    if (!$group) {
        $stmt = $pdo->prepare(
            "INSERT INTO brand_asset_groups 
             (company_id, title, asset_type, theme, is_public, status, sort_order, created_at, updated_at)
             VALUES (?, 'Logo', 'logo', 'default', 1, 'approved', 0, ?, ?)"
        );
        $stmt->execute([$compId, $now, $now]);
        $groupId = $pdo->lastInsertId();
        $groupsCreated++;
    } else {
        $groupId = $group['id'];
    }

    // Check if PNG already registered
    $stmt = $pdo->prepare(
        "SELECT id FROM brand_asset_files 
         WHERE asset_group_id = ? AND original_filename = 'logo.png' AND deleted_at IS NULL"
    );
    $stmt->execute([$groupId]);
    if ($stmt->fetch()) {
        echo "⏭  Already registered: {$slug}\n";
        $skipped++;
        continue;
    }

    // Determine if should be primary
    $stmt = $pdo->prepare(
        "SELECT COUNT(*) as cnt FROM brand_asset_files 
         WHERE asset_group_id = ? AND deleted_at IS NULL"
    );
    $stmt->execute([$groupId]);
    $existingCount = $stmt->fetch()['cnt'];
    $isPrimary = ($existingCount === 0) ? 1 : 0;

    $token        = bin2hex(random_bytes(16));
    $cacheVersion = date('YmdHis');
    $storagePath  = "uploads/{$slug}/logo.png";
    $storedName   = 'logo.png';

    $stmt = $pdo->prepare(
        "INSERT INTO brand_asset_files 
         (asset_group_id, original_filename, stored_filename, storage_path, cdn_path, extension, 
          file_size, mime_type, is_primary, is_public, is_cdn_enabled, public_token, 
          cache_version, created_at, updated_at)
         VALUES (?, ?, ?, ?, ?, 'png', ?, 'image/png', ?, 1, 1, ?, ?, ?, ?)"
    );
    $stmt->execute([
        $groupId, 'logo.png', $storedName, $storagePath, $storagePath,
        $fileSize, $isPrimary, $token, $cacheVersion, $now, $now
    ]);

    $sizeKb = round($fileSize / 1024, 1);
    echo "✅ {$slug} → logo.png ({$sizeKb} KB, primary={$isPrimary})\n";
    $filesAdded++;
}

echo "\n📊 Groups created: {$groupsCreated} | PNGs added: {$filesAdded} | Skipped: {$skipped}\n";

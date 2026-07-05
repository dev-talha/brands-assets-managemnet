<?php
/**
 * Move PNG files to separate logo groups so they show as individual cards.
 * Each brand will have: "Logo (SVG)" group + "Logo (PNG)" group
 * Run: php scripts/split_png_to_own_group.php
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
$moved = 0;
$skipped = 0;

// Find all PNG files that are NOT primary (sitting in a group with SVG)
$pngFiles = $pdo->query(
    "SELECT f.*, g.company_id, g.theme, g.is_public, g.status, c.slug
     FROM brand_asset_files f
     JOIN brand_asset_groups g ON g.id = f.asset_group_id
     JOIN companies c ON c.id = g.company_id
     WHERE f.extension = 'png'
       AND f.is_primary = 0
       AND f.deleted_at IS NULL
       AND g.asset_type = 'logo'
       AND g.deleted_at IS NULL"
)->fetchAll();

foreach ($pngFiles as $file) {
    $compId = $file['company_id'];
    $slug   = $file['slug'];

    // Check if a dedicated PNG logo group already exists
    $stmt = $pdo->prepare(
        "SELECT id FROM brand_asset_groups 
         WHERE company_id = ? AND asset_type = 'logo' AND title = 'Logo (PNG)' AND deleted_at IS NULL"
    );
    $stmt->execute([$compId]);
    $existingGroup = $stmt->fetch();

    if ($existingGroup) {
        echo "⏭  PNG group already exists: {$slug}\n";
        $skipped++;
        continue;
    }

    // Create a new group for PNG
    $groupSlug = $slug . '-logo-png';
    $stmt = $pdo->prepare(
        "INSERT INTO brand_asset_groups 
         (company_id, title, slug, asset_type, theme, description, is_public, status, sort_order, created_at, updated_at)
         VALUES (?, 'Logo (PNG)', ?, 'logo', 'default', 'Raster PNG version of the logo', 1, 'approved', 1, ?, ?)"
    );
    $stmt->execute([$compId, $groupSlug, $now, $now]);
    $newGroupId = $pdo->lastInsertId();

    // Move PNG file to new group and mark as primary
    $stmt = $pdo->prepare(
        "UPDATE brand_asset_files 
         SET asset_group_id = ?, is_primary = 1, updated_at = ?
         WHERE id = ?"
    );
    $stmt->execute([$newGroupId, $now, $file['id']]);

    echo "✅ {$slug}: PNG moved to new group (group_id={$newGroupId})\n";
    $moved++;
}

echo "\n📊 Moved: {$moved} | Skipped: {$skipped}\n";

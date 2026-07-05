<?php
define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/vendor/autoload.php';

$envFile = BASE_PATH . '/.env';
if (file_exists($envFile)) {
    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        if (strpos(trim($line), '#') === 0 || !strpos($line, '=')) continue;
        [$key, $val] = explode('=', $line, 2);
        $_ENV[trim($key)] = trim($val, '"\'');
    }
}

$dbPath = BASE_PATH . '/' . ($_ENV['DB_DATABASE'] ?? 'database/database.sqlite');
$pdo = new PDO('sqlite:' . $dbPath);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$now = date('Y-m-d H:i:s');

$brands = [
    [
        'name' => 'Acme Corporation',
        'slug' => 'acme-corporation',
        'domain' => 'acmecorp.example.com',
        'color' => '#1d4ed8'
    ],
    [
        'name' => 'Globex Inc',
        'slug' => 'globex-inc',
        'domain' => 'globex.example.com',
        'color' => '#b91c1c'
    ],
    [
        'name' => 'Initech',
        'slug' => 'initech',
        'domain' => 'initech.example.com',
        'color' => '#047857'
    ]
];

foreach ($brands as $index => $b) {
    // Insert Company
    $stmt = $pdo->prepare("INSERT INTO companies (name, slug, domain, status, created_at, updated_at) VALUES (?, ?, ?, 'active', ?, ?)");
    $stmt->execute([$b['name'], $b['slug'], $b['domain'], $now, $now]);
    $companyId = $pdo->lastInsertId();

    // Create Avatar
    $avatarSvg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><rect width="100" height="100" rx="20" fill="'.$b['color'].'"/><text x="50" y="65" font-size="45" font-family="sans-serif" font-weight="bold" fill="white" text-anchor="middle">'.substr($b['name'],0,1).'</text></svg>';
    $avatarDir = BASE_PATH . '/storage/uploads/companies/avatar';
    if (!is_dir($avatarDir)) mkdir($avatarDir, 0755, true);
    $avatarPath = 'companies/avatar/' . $b['slug'] . '.svg';
    file_put_contents(BASE_PATH . '/storage/uploads/' . $avatarPath, $avatarSvg);
    $pdo->prepare("UPDATE companies SET avatar_image_path = ? WHERE id = ?")->execute([$avatarPath, $companyId]);

    // Insert Default Group
    $pdo->prepare("INSERT INTO brand_asset_groups (company_id, title, slug, asset_type, theme, created_at, updated_at) VALUES (?, 'Primary Logos', 'primary-logos', 'logo', 'default', ?, ?)")->execute([$companyId, $now, $now]);
    $groupId = $pdo->lastInsertId();

    // Create Logo Asset
    $logoSvg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 300 100"><rect width="300" height="100" fill="transparent"/><text x="150" y="65" font-size="40" font-family="sans-serif" font-weight="bold" fill="'.$b['color'].'" text-anchor="middle">'.$b['name'].'</text></svg>';
    $logoDir = BASE_PATH . '/storage/uploads/' . $b['slug'];
    if (!is_dir($logoDir)) mkdir($logoDir, 0755, true);
    $logoPath = $b['slug'] . '/logo.svg';
    file_put_contents(BASE_PATH . '/storage/uploads/' . $logoPath, $logoSvg);
    $pdo->prepare("INSERT INTO brand_asset_files (asset_group_id, original_filename, stored_filename, storage_path, public_token, extension, mime_type, file_size, cdn_path, cache_version, is_primary, created_at, updated_at) VALUES (?, 'logo.svg', 'logo.svg', ?, ?, 'svg', 'image/svg+xml', ?, ?, 'v1', 1, ?, ?)")->execute([$groupId, $logoPath, bin2hex(random_bytes(16)), strlen($logoSvg), 'cdn-internal/asset/'.bin2hex(random_bytes(8)), $now, $now]);
    
    // Add primary color
    $pdo->prepare("INSERT INTO brand_colors (company_id, name, hex_code, color_type, created_at, updated_at) VALUES (?, 'Primary', ?, 'primary', ?, ?)")->execute([$companyId, $b['color'], $now, $now]);
}

echo "✅ Generated 3 generic demo brands!\n";

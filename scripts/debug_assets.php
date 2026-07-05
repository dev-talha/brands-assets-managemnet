<?php
define('BASE_PATH', dirname(__DIR__));
$pdo = new PDO('sqlite:' . BASE_PATH . '/database/database.sqlite');
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

// Check godigital as sample
$company = $pdo->query("SELECT id, slug FROM companies WHERE slug='godigital'")->fetch();
echo "Company: godigital (id={$company['id']})\n\n";

$groups = $pdo->prepare("SELECT * FROM brand_asset_groups WHERE company_id=? AND asset_type='logo' AND deleted_at IS NULL");
$groups->execute([$company['id']]);
foreach ($groups->fetchAll() as $g) {
    echo "Group id={$g['id']} title={$g['title']} theme={$g['theme']} status={$g['status']} is_public={$g['is_public']}\n";
    $files = $pdo->prepare("SELECT id, original_filename, extension, is_primary, is_public, is_cdn_enabled, public_token FROM brand_asset_files WHERE asset_group_id=? AND deleted_at IS NULL");
    $files->execute([$g['id']]);
    foreach ($files->fetchAll() as $f) {
        echo "  File id={$f['id']} name={$f['original_filename']} ext={$f['extension']} primary={$f['is_primary']} public={$f['is_public']} cdn={$f['is_cdn_enabled']}\n";
    }
}

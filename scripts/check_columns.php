<?php
define('BASE_PATH', dirname(__DIR__));
$pdo = new PDO('sqlite:' . BASE_PATH . '/database/database.sqlite');
echo "=== brand_asset_files ===\n";
foreach($pdo->query('PRAGMA table_info(brand_asset_files)')->fetchAll(PDO::FETCH_ASSOC) as $c) {
    echo $c['cid'] . ': ' . $c['name'] . ' (' . $c['type'] . ")\n";
}
echo "\n=== brand_asset_groups ===\n";
foreach($pdo->query('PRAGMA table_info(brand_asset_groups)')->fetchAll(PDO::FETCH_ASSOC) as $c) {
    echo $c['cid'] . ': ' . $c['name'] . ' (' . $c['type'] . ")\n";
}

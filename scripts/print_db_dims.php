<?php
define('BASE_PATH', dirname(__DIR__));
$pdo = new PDO('sqlite:' . BASE_PATH . '/database/database.sqlite');
$f = $pdo->query("SELECT id, original_filename, storage_path, width, height FROM brand_asset_files WHERE original_filename='logo.png'")->fetchAll(PDO::FETCH_ASSOC);
print_r($f);

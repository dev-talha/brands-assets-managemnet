<?php
/**
 * Migration runner - executes all SQL migration files in order.
 */

define('BASE_PATH', dirname(__DIR__));
require BASE_PATH . '/app/Support/helpers.php';

// Load env
$envFile = BASE_PATH . '/.env';
if (!file_exists($envFile)) {
    $example = BASE_PATH . '/.env.example';
    if (file_exists($example)) copy($example, $envFile);
}
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if (str_starts_with($line, '#') || !str_contains($line, '=')) continue;
        [$key, $value] = explode('=', $line, 2);
        $_ENV[trim($key)] = trim($value, " \t\n\r\0\x0B\"'");
    }
}

$dbPath = BASE_PATH . '/' . env('DB_DATABASE', 'database/database.sqlite');
$dir = dirname($dbPath);
if (!is_dir($dir)) mkdir($dir, 0755, true);

echo "Database path: {$dbPath}\n";

$pdo = new PDO('sqlite:' . $dbPath, null, null, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
]);

$pdo->exec('PRAGMA foreign_keys = ON;');
$pdo->exec('PRAGMA journal_mode = WAL;');

// Run migrations
$migrationDir = BASE_PATH . '/database/migrations';
$files = glob($migrationDir . '/*.sql');
sort($files);

echo "Running migrations...\n\n";

foreach ($files as $file) {
    $filename = basename($file);
    echo "  Running: {$filename}... ";
    
    $sql = file_get_contents($file);
    
    // Split by semicolons for multiple statements
    $statements = array_filter(
        array_map('trim', explode(';', $sql)),
        fn($s) => !empty($s)
    );
    
    foreach ($statements as $statement) {
        $pdo->exec($statement);
    }
    
    echo "✓\n";
}

echo "\n✅ All migrations completed successfully!\n";

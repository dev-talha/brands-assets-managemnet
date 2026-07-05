<?php
/**
 * Seeder - creates the default Super Admin user from .env values.
 */

define('BASE_PATH', dirname(__DIR__));
require BASE_PATH . '/app/Support/helpers.php';

// Load env
$envFile = BASE_PATH . '/.env';
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

if (!file_exists($dbPath)) {
    echo "❌ Database not found. Run migrate.php first.\n";
    exit(1);
}

$pdo = new PDO('sqlite:' . $dbPath, null, null, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
]);

$pdo->exec('PRAGMA foreign_keys = ON;');

$name = env('DEFAULT_ADMIN_NAME', 'Super Admin');
$email = env('DEFAULT_ADMIN_EMAIL', 'admin@example.com');
$password = env('DEFAULT_ADMIN_PASSWORD', 'password');

echo "Seeding Super Admin user...\n";
echo "  Name: {$name}\n";
echo "  Email: {$email}\n\n";

// Check if user already exists
$stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
$stmt->execute([$email]);
$existing = $stmt->fetch();

if ($existing) {
    echo "⚠ User with email {$email} already exists (ID: {$existing['id']}). Skipping.\n";
} else {
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $now = date('Y-m-d H:i:s');

    $stmt = $pdo->prepare(
        'INSERT INTO users (name, email, password_hash, role, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?)'
    );
    $stmt->execute([$name, $email, $hash, 'super_admin', 'active', $now, $now]);
    
    echo "✅ Super Admin created successfully!\n";
}

echo "\nDone.\n";

<?php
/* 
LOCALDE ÇALIŞTIRMAK İÇİN BU KODU KULLANIN!

declare(strict_types=1); $dbHost = 'localhost';
$dbName = 'mustafa_blog';
$dbUser = 'root';
$dbPass = '';
$dbCharset = 'utf8mb4'; $dsn = "mysql:host={$dbHost};dbname={$dbName};charset={$dbCharset}";
$options = [ PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, PDO::ATTR_EMULATE_PREPARES => false,
]; try { $pdo = new PDO($dsn, $dbUser, $dbPass, $options);
} catch (PDOException $e) { http_response_code(500); echo 'Database connection failed.'; exit;
}

*/

/*
RAILWAY GİBİ PLATFORMLAR İÇİN BU ŞEKİLDE
*/

declare(strict_types=1);

/**
 * Railway MySQL bağlantısı
 * DATABASE_URL veya MYSQL_URL üzerinden bağlanır
 * Örnek URL: mysql://user:pass@host:port/dbname
 */

$databaseUrl = getenv('DATABASE_URL') ?: getenv('MYSQL_URL');

if (!$databaseUrl) {
    http_response_code(500);
    echo 'Database configuration not found.';
    exit;
}

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $parts = parse_url($databaseUrl);

    $host = $parts['host'] ?? '';
    $port = (int)($parts['port'] ?? 3306);
    $user = $parts['user'] ?? '';
    $pass = $parts['pass'] ?? '';
    $db   = isset($parts['path']) ? ltrim($parts['path'], '/') : '';

    if (!$host || !$user || !$db) {
        throw new RuntimeException('Invalid database URL.');
    }

    $dsn = "mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass, $options);

} catch (Throwable $e) {
    http_response_code(500);

    // Prod ortamda hata detayı göstermiyoruz
    // error_log('DB Error: ' . $e->getMessage());

    echo 'Database connection failed.';
    exit;
}


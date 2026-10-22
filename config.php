<?php
declare(strict_types=1);

$hostname = "localhost";
$username = "root";
$password = "";
$database = "creditready";

/*
 * CreditReady configuration.
 * Set these environment variables or update the local defaults for your XAMPP setup.
 */
define('APP_NAME', 'CreditReady');
define('APP_BASE_URL', '/CreditReady'); // Example: '/CreditReady' if hosted in htdocs/CreditReady
define('DB_HOST', getenv('CREDITREADY_DB_HOST') ?: '127.0.0.1');
define('DB_NAME', getenv('CREDITREADY_DB_NAME') ?: 'creditready');
define('DB_USER', getenv('CREDITREADY_DB_USER') ?: 'root');
define('DB_PASS', getenv('CREDITREADY_DB_PASS') ?: '');

if (session_status() !== PHP_SESSION_ACTIVE) {
    ini_set('session.use_strict_mode', '1');
    session_start();
}

function db(): PDO {
    static $pdo = null;
    if ($pdo instanceof PDO) return $pdo;
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
    return $pdo;
}

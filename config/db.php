<?php
// config/db.php
require_once __DIR__ . '/config.php';

try {
    // Resolve host -> IPv4 (evita socket local)
    $resolvedHost = DB_HOST;
    if (!filter_var(DB_HOST, FILTER_VALIDATE_IP)) {
        $ip = @gethostbyname(DB_HOST);
        if ($ip && $ip !== DB_HOST) $resolvedHost = $ip;
    }

    $dsn = 'mysql:host=' . $resolvedHost . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=utf8mb4';

    $pdo = new PDO(
        $dsn,
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_PERSISTENT => false,
            PDO::ATTR_TIMEOUT => 10,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
        ]
    );
} catch (PDOException $e) {
    if (APP_ENV !== 'production') {
        die('Erro ao conectar ao MySQL: ' . $e->getMessage() . ' | DSN: ' . (isset($dsn) ? $dsn : ''));
    }
    die('Erro ao conectar ao MySQL. Verifique config/config.php.');
}
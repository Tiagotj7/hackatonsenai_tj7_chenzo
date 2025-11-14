<?php
// config/config.php
require_once __DIR__ . '/env.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

define('APP_NAME', env('APP_NAME', 'Senai Service Manager'));

// DB
define('DB_HOST', env('DB_HOST', 'localhost'));
define('DB_NAME', env('DB_NAME', 'senai_chamados'));
define('DB_USER', env('DB_USER', 'root'));
define('DB_PASS', env('DB_PASS', ''));

// BASE_URL
$base = env('BASE_URL', null);
if (!$base) {
    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
          || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');
    $scheme = $https ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $dir = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/\\');
    $base = $scheme . '://' . $host . ($dir ? $dir : '');
}
define('BASE_URL', rtrim($base, '/'));

// Uploads e Email
define('UPLOAD_DIR', env('UPLOAD_DIR', dirname(__DIR__) . '/uploads'));
define('MAX_UPLOAD_MB', (int)env('MAX_UPLOAD_MB', 2));

define('EMAIL_ENABLED', filter_var(env('EMAIL_ENABLED', 'false'), FILTER_VALIDATE_BOOLEAN));
define('EMAIL_FROM', env('EMAIL_FROM', 'no-reply@senai.local'));
define('EMAIL_FROM_NAME', env('EMAIL_FROM_NAME', 'Senai Service Manager'));
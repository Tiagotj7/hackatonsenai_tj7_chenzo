<?php
// config/config.php
require_once __DIR__ . '/env.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

define('APP_ENV', env('APP_ENV', 'production'));
define('APP_NAME', env('APP_NAME', 'Senai Service Manager'));

// Carrega .env simples se existir (opcional)
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        [$k, $v] = array_map('trim', explode('=', $line, 2) + [null, null]);
        if ($k && $v !== null) putenv("$k=$v");
    }
}

// Config visível para uso local com XAMPP
// Ajuste estes valores conforme seu ambiente local
define('DB_HOST', 'sql212.infinityfree.com');
define('DB_NAME', 'if0_40352073_db_agendeaqui'); // <-- altere para o nome do seu banco local
define('DB_USER', 'if0_40352073');
define('DB_PASS', 'your_password_here'); // XAMPP padrão: senha vazia
define('DB_CHARSET', 'utf8mb4');

// Validação mínima
if (!DB_HOST || !DB_NAME || !DB_USER) {
    error_log('Configuração do DB incorreta em config/config.php');
    die('Erro de configuração do banco de dados. Verifique config/config.php.');
}

// Criar conexão PDO com tratamento de erro e retry básico para 429/timeout
$dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
$pdoOptions = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_PERSISTENT => false,
];

try {
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $pdoOptions);
} catch (PDOException $e) {
    error_log('Falha ao conectar ao DB: ' . $e->getMessage());
    die('Não foi possível conectar ao banco de dados. Verifique config/config.php.');
}

// Função helper para retornar PDO (opcional)
function db(): PDO
{
    global $pdo;
    return $pdo;
}

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

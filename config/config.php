<?php
// config/config.php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Ambiente e App
define('APP_ENV', 'production');
define('APP_NAME', 'Senai Service Manager');

// AJUSTE AQUI: seu domínio do InfinityFree (com https se tiver SSL)
define('BASE_URL', 'https://agendeaqui.rf.gd/'); 
// MySQL (InfinityFree)
define('DB_HOST', 'sql212.infinityfree.com');
define('DB_PORT', 3306);
define('DB_NAME', 'if0_40352073_db_agendeaqui');
define('DB_USER', 'if0_40352073');
define('DB_PASS', 'xldkrDW2IYPMMuH');

// Uploads e E-mail
define('UPLOAD_DIR', dirname(__DIR__) . '/uploads');
define('MAX_UPLOAD_MB', 2);

define('EMAIL_ENABLED', false);
define('EMAIL_FROM', 'no-reply@senai.local');
define('EMAIL_FROM_NAME', 'Senai Service Manager');
<?php
// config/config.php (InfinityFree)
if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }

define('APP_ENV', 'production');
define('APP_NAME', 'Senai Service Manager');
define('BASE_URL', 'https://agendeaqui.rf.gd'); // https no domínio

// MySQL InfinityFree
define('DB_HOST', 'sql212.infinityfree.com');
define('DB_PORT', 3306);
define('DB_NAME', 'if0_40352073_db_agendeaqui');
define('DB_USER', 'if0_40352073');
define('DB_PASS', 'xldkrDW2IYPMMuH');

// Uploads & E-mail
define('UPLOAD_DIR', dirname(__DIR__) . '/uploads');
define('MAX_UPLOAD_MB', 2);
define('EMAIL_ENABLED', false);
define('EMAIL_FROM', 'no-reply@senai.local');
define('EMAIL_FROM_NAME', 'Senai Service Manager');
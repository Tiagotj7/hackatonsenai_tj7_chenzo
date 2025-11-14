<?php
// config/config.php
// Ajuste conforme seu ambiente XAMPP
define('DB_HOST', 'localhost');
define('DB_NAME', 'senai_chamados');
define('DB_USER', 'root');
define('DB_PASS', ''); // no XAMPP, normalmente vazio

// URL base (ajuste se necessário)
define('BASE_URL', '/');

// Uploads
define('UPLOAD_DIR', __DIR__ . '/../uploads');
define('UPLOAD_BASE', BASE_URL . '/uploads');

// E-mail (extra): se não tiver SMTP local, mantenha false para log local
define('EMAIL_ENABLED', false);
define('EMAIL_FROM', 'no-reply@senai.local');
define('EMAIL_FROM_NAME', 'Sistema de Solicitações SENAI');
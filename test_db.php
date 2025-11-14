<?php
require_once __DIR__ . '/config/config.php';
echo "<pre>";
echo "APP_NAME: " . APP_NAME . PHP_EOL;
echo "BASE_URL: " . BASE_URL . PHP_EOL;
echo "DB_HOST: " . DB_HOST . PHP_EOL;
echo "DB_PORT: " . DB_PORT . PHP_EOL;
echo "DB_NAME: " . DB_NAME . PHP_EOL;
echo "DB_USER: " . DB_USER . PHP_EOL;

require_once __DIR__ . '/config/db.php';
echo "Conexão OK!" . PHP_EOL;
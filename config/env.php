<?php
// config/env.php
function env($key, $default = null) {
    static $loaded = false;
    static $store = [];

    if (!$loaded) {
        $path = dirname(__DIR__) . '/.env'; // arquivo .env na raiz do projeto
        if (is_file($path) && is_readable($path)) {
            $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                $line = trim($line);
                if ($line === '' || strpos($line, '#') === 0 || strpos($line, ';') === 0) continue;
                if (strpos($line, '=') === false) continue;
                list($k, $v) = array_map('trim', explode('=', $line, 2));
                // remove aspas ao redor
                $v = trim($v, " \t\n\r\0\x0B\"'");
                $store[$k] = $v;
                $_ENV[$k] = $v;
            }
        }
        $loaded = true;
    }

    if (array_key_exists($key, $_ENV)) return $_ENV[$key];
    if (array_key_exists($key, $store)) return $store[$key];
    $val = getenv($key);
    return $val !== false ? $val : $default;
}
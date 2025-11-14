<?php
// config/helpers.php
require_once __DIR__ . '/config.php';

// Fallbacks seguros
if (!defined('APP_NAME')) {
  define('APP_NAME', 'SENAI Chamados');
}
if (!defined('MAX_UPLOAD_MB')) {
  define('MAX_UPLOAD_MB', 2);
}

function app_name() {
  return defined('APP_NAME') ? APP_NAME : 'SENAI Chamados';
}
function max_upload_mb() {
  $v = defined('MAX_UPLOAD_MB') ? (int)MAX_UPLOAD_MB : 2;
  return $v > 0 ? $v : 2;
}

function base_url($path = '') {
  $base = defined('BASE_URL') ? BASE_URL : (function () {
    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
      || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');
    $scheme = $https ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $dir = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/\\');
    return $scheme . '://' . $host . ($dir ? $dir : '');
  })();
  return rtrim($base, '/') . '/' . ltrim($path, '/');
}

function redirect($path) {
  header('Location: ' . base_url($path));
  exit;
}

function e($str) {
  return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

function csrf_token() {
  if (session_status() !== PHP_SESSION_ACTIVE) session_start();
  if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(16));
  }
  return $_SESSION['csrf'];
}

function verify_csrf() {
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf'] ?? '';
    if (!$token || !hash_equals($_SESSION['csrf'] ?? '', $token)) {
      die('CSRF inválido.');
    }
  }
}

function flash($type, $message) {
  if (session_status() !== PHP_SESSION_ACTIVE) session_start();
  $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
}

function get_flash() {
  if (session_status() !== PHP_SESSION_ACTIVE) session_start();
  $msgs = $_SESSION['flash'] ?? [];
  unset($_SESSION['flash']);
  return $msgs;
}

function gerar_protocolo(PDO $pdo) {
  $stmt = $pdo->query("SELECT COUNT(*) AS total FROM tickets WHERE DATE(opened_at)=CURDATE()");
  $seq = (int)$stmt->fetch()['total'] + 1;
  return 'T' . date('Ymd') . '-' . str_pad((string)$seq, 4, '0', STR_PAD_LEFT);
}

function upload_imagem($file) {
  if (empty($file['name'])) return null;

  $maxBytes = max_upload_mb() * 1024 * 1024;

  if (!is_dir(UPLOAD_DIR)) @mkdir(UPLOAD_DIR, 0775, true);

  // Descobrir MIME real
  $mime = '';
  if (function_exists('finfo_open')) {
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
  } elseif (function_exists('mime_content_type')) {
    $mime = mime_content_type($file['tmp_name']);
  }

  $allowed = ['image/jpeg'=>'jpg','image/png'=>'png','image/gif'=>'gif'];
  if (!isset($allowed[$mime])) {
    flash('error', 'Tipo de arquivo inválido. Use JPG, PNG ou GIF.');
    return false;
  }

  if ($file['size'] > $maxBytes) {
    flash('error', 'Arquivo excede ' . max_upload_mb() . 'MB.');
    return false;
  }

  $name = uniqid('img_', true) . '.' . $allowed[$mime];
  $dest = rtrim(UPLOAD_DIR, '/\\') . '/' . $name;

  if (!move_uploaded_file($file['tmp_name'], $dest)) {
    flash('error', 'Falha ao salvar a imagem.');
    return false;
  }

  return 'uploads/' . $name; // caminho relativo para exibir
}
<?php
// config/helpers.php
require_once __DIR__ . '/config.php';

function base_url($path = '') {
  return rtrim(BASE_URL, '/') . '/' . ltrim($path, '/');
}

function redirect($path) {
  header('Location: ' . base_url($path));
  exit;
}

function e($str) {
  return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

function csrf_token() {
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
  $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
}

function get_flash() {
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

  if (!is_dir(UPLOAD_DIR)) @mkdir(UPLOAD_DIR, 0775, true);

  $finfo = new finfo(FILEINFO_MIME_TYPE);
  $mime = $finfo->file($file['tmp_name']);
  $allowed = ['image/jpeg'=>'jpg','image/png'=>'png','image/gif'=>'gif'];
  if (!isset($allowed[$mime])) {
    flash('error', 'Tipo de arquivo inválido. Use JPG, PNG ou GIF.');
    return false;
  }

  $max = MAX_UPLOAD_MB * 1024 * 1024;
  if ($file['size'] > $max) {
    flash('error', 'Arquivo excede ' . MAX_UPLOAD_MB . 'MB.');
    return false;
  }

  $name = uniqid('img_', true) . '.' . $allowed[$mime];
  $dest = UPLOAD_DIR . '/' . $name;
  if (!move_uploaded_file($file['tmp_name'], $dest)) {
    flash('error', 'Falha ao salvar a imagem.');
    return false;
  }
  return 'uploads/' . $name;
}
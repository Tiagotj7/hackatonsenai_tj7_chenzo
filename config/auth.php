<?php
// config/auth.php
function is_admin_logged() {
  return !empty($_SESSION['admin_user']);
}
function current_admin() {
  return $_SESSION['admin_user'] ?? null;
}
function require_admin() {
  if (!is_admin_logged()) {
    flash('warning', 'Faça login para acessar.');
    redirect('admin/login.php');
  }
}
<?php
// admin/header.php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../config/auth.php';
?>
<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <title><?php echo isset($pageTitle) ? e($pageTitle) . ' - ' : ''; ?><?php echo e(app_name()); ?></title>
  <link rel="stylesheet" href="<?php echo base_url('assets/css/style.css'); ?>">
  <link rel="shortcut icon" href="<?php echo base_url('assets/images/favicon.ico'); ?>" type="image/x-icon">
  <meta name="viewport" content="width=device-width,initial-scale=1">
</head>
<body>
  <nav class="navbar">
    <div class="container">
      <h1><?php echo e(app_name()); ?></h1>
      <div class="actions">
        <label style="display:flex;align-items:center;gap:6px;cursor:pointer;">
          <input type="checkbox" id="darkToggle"> Modo escuro
        </label>
        <?php if (is_admin_logged()): $adm = current_admin(); ?>
          <span style="color:var(--muted);">Olá, <?php echo e($adm['nome']); ?></span>
          <a class="btn" href="<?php echo base_url('admin/dashboard.php'); ?>">Dashboard</a>
          <a class="btn" href="<?php echo base_url('admin/request.php'); ?>">Tickets</a>
          <a class="btn" href="<?php echo base_url('admin/relatorios.php'); ?>">Relatórios</a>
          <a class="btn danger" href="<?php echo base_url('admin/logout.php'); ?>">Sair</a>
        <?php else: ?>
          <a class="btn" href="<?php echo base_url('admin/login.php'); ?>">Voltar</a>
        <?php endif; ?>
      </div>
    </div>
  </nav>
  <div class="container">
    <?php foreach (get_flash() as $f): ?>
      <div class="alert <?php echo e($f['type']); ?>"><?php echo e($f['message']); ?></div>
    <?php endforeach; ?>
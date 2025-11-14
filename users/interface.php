<?php
require_once __DIR__ . '/config/helpers.php';
?>
<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <title><?php echo APP_NAME; ?> - Início</title>
  <link rel="stylesheet" href="<?php echo base_url('assets/css/style.css'); ?>">
</head>
<body>
  <nav class="navbar">
    <div class="container">
      <h1><?php echo APP_NAME; ?></h1>
      <div class="actions">
        <label style="display:flex;align-items:center;gap:6px;cursor:pointer;">
          <input type="checkbox" id="darkToggle"> Modo escuro
        </label>
        <a class="btn" href="<?php echo base_url('admin/login.php'); ?>">Admin</a>
      </div>
    </div>
  </nav>
  <div class="container">
    <?php foreach (get_flash() as $f): ?>
      <div class="alert <?php echo e($f['type']); ?>"><?php echo e($f['message']); ?></div>
    <?php endforeach; ?>

    <div class="card">
      <h2>Bem-vindo ao Sistema de Chamados Internos</h2>
      <p>Registre, acompanhe e conclua solicitações de TI e Manutenção no SENAI.</p>
    </div>

    <div class="grid cols-2">
      <div class="card">
        <h3>Sou users (Professor/Funcionário)</h3>
        <p>Abra um novo chamado ou acompanhe suas solicitações pelo número de matrícula.</p>
        <div style="display:flex; gap:8px; flex-wrap:wrap;">
          <a class="btn primary" href="<?php echo base_url('users/create.php'); ?>">Abrir Solicitação</a>
          <a class="btn" href="<?php echo base_url('users/minhas.php'); ?>">Minhas Solicitações</a>
        </div>
      </div>
      <div class="card">
        <h3>Administrador / Setor Responsável</h3>
        <p>Acesse o painel para gerenciar as solicitações, filtrar demandas e atualizar status.</p>
        <a class="btn success" href="<?php echo base_url('admin/login.php'); ?>">Entrar no Painel</a>
      </div>
    </div>

    <div class="footer">© <?php echo date('Y'); ?> SENAI - Sistema de Chamados</div>
  </div>
  <script src="<?php echo base_url('assets/js/app.js'); ?>"></script>
</body>
</html>
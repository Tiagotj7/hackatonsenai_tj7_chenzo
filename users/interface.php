<?php
require_once __DIR__ . '/../config/helpers.php';
?>
<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <title><?php echo e(app_name()); ?> - Área do Solicitante</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="<?php echo base_url('assets/css/style.css'); ?>">
</head>
<body>
  <header class="navbar">
    <div class="container">
      <h1>Área do Solicitante</h1>
      <div class="actions">
        <label style="display:flex;align-items:center;gap:6px;cursor:pointer;"><input type="checkbox" id="darkToggle"> Modo escuro</label>
        <a class="btn" href="<?php echo base_url('index.php'); ?>">Início</a>
      </div>
    </div>
  </header>
  <div class="container">
    <?php foreach (get_flash() as $f): ?><div class="alert <?php echo e($f['type']); ?>"><?php echo e($f['message']); ?></div><?php endforeach; ?>
    <div class="grid cols-2">
      <div class="card"><h3>Abrir nova solicitação</h3><p>Informe os dados do problema, prioridade e categoria.</p><a class="btn primary" href="<?php echo base_url('users/create.php'); ?>">Abrir Solicitação</a></div>
      <div class="card"><h3>Minhas solicitações</h3><p>Consulte o status dos seus chamados pela matrícula.</p><a class="btn" href="<?php echo base_url('users/myrequest.php'); ?>">Consultar</a></div>
    </div>
  </div>
  <script src="<?php echo base_url('assets/js/app.js'); ?>"></script>
</body>
</html>
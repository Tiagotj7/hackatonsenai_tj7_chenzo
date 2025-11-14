<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/helpers.php';

$matricula = trim($_GET['matricula'] ?? '');
$tickets = [];
if ($matricula) {
  $sql = "SELECT t.*, rt.nome AS tipo_nome, s.nome AS setor_nome, ts.nome AS status_nome
          FROM tickets t
          JOIN request_types rt ON rt.id=t.tipo_id
          JOIN sectors s ON s.id=t.setor_id
          JOIN ticket_status ts ON ts.id=t.status_id
          WHERE t.matricula = :mat
          ORDER BY t.opened_at DESC";
  $st = $pdo->prepare($sql);
  $st->execute([':mat'=>$matricula]);
  $tickets = $st->fetchAll();
}
?>
<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <title>Minhas Solicitações - <?php echo APP_NAME; ?></title>
  <link rel="stylesheet" href="<?php echo base_url('assets/css/style.css'); ?>">
</head>
<body>
  <nav class="navbar">
    <div class="container">
      <h1>Minhas Solicitações</h1>
      <div class="actions">
        <label style="display:flex;align-items:center;gap:6px;cursor:pointer;">
          <input type="checkbox" id="darkToggle"> Modo escuro
        </label>
        <a class="btn" href="<?php echo base_url('interface.php'); ?>">Início</a>
      </div>
    </div>
  </nav>
  <div class="container">
    <?php foreach (get_flash() as $f): ?>
      <div class="alert <?php echo e($f['type']); ?>"><?php echo e($f['message']); ?></div>
    <?php endforeach; ?>

    <div class="card">
      <form method="get">
        <div class="form-group">
          <label>Informe sua matrícula</label>
          <input type="text" name="matricula" required value="<?php echo e($matricula); ?>">
        </div>
        <button class="btn primary" type="submit">Buscar</button>
      </form>
    </div>

    <?php if ($matricula): ?>
      <div class="card">
        <h3>Resultados para: <?php echo e($matricula); ?></h3>
        <?php if (!$tickets): ?>
          <p>Nenhuma solicitação encontrada.</p>
        <?php else: ?>
          <table class="table">
            <thead>
              <tr>
                <th>Protocolo</th><th>Categoria</th><th>Setor</th><th>Prioridade</th>
                <th>Status</th><th>Abertura</th><th>Atualização</th><th>Ações</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($tickets as $t):
                $p=strtolower($t['prioridade']); $cls=$p==='urgente'?'urgente':($p==='média'?'media':'baixa'); ?>
                <tr>
                  <td><?php echo e($t['protocolo']); ?></td>
                  <td><?php echo e($t['tipo_nome']); ?></td>
                  <td><?php echo e($t['setor_nome']); ?></td>
                  <td><span class="badge <?php echo $cls; ?>"><?php echo e($t['prioridade']); ?></span></td>
                  <td><?php echo e($t['status_nome']); ?></td>
                  <td><?php echo e(date('d/m/Y H:i', strtotime($t['opened_at']))); ?></td>
                  <td><?php echo e(date('d/m/Y H:i', strtotime($t['updated_at']))); ?></td>
                  <td><a class="btn" href="<?php echo base_url('solicitante/detalhes.php?protocolo='.urlencode($t['protocolo'])); ?>">Ver</a></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php endif; ?>
      </div>
    <?php endif; ?>
  </div>
  <script src="<?php echo base_url('assets/js/app.js'); ?>"></script>
</body>
</html>
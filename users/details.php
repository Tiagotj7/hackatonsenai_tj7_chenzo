<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/helpers.php';

$protocolo = trim($_GET['protocolo'] ?? '');
if (!$protocolo) { flash('warning','Protocolo não informado.'); redirect('users/myrequest.php'); }

$sql = "SELECT t.*, rt.nome AS tipo_nome, s.nome AS setor_nome, ts.nome AS status_nome
        FROM tickets t
        JOIN request_types rt ON rt.id=t.tipo_id
        JOIN sectors s ON s.id=t.setor_id
        JOIN ticket_status ts ON ts.id=t.status_id
        WHERE t.protocolo=:p";
$st = $pdo->prepare($sql); $st->execute([':p'=>$protocolo]); $ticket = $st->fetch();
if (!$ticket) { flash('error','Solicitação não encontrada.'); redirect('users/myrequest.php'); }

$movs = $pdo->prepare("SELECT tm.*, ua.nome AS admin_nome, ts.nome AS status_nome
                       FROM ticket_movements tm
                       JOIN ticket_status ts ON ts.id=tm.status_id
                       LEFT JOIN users_admin ua ON ua.id=tm.user_id
                       WHERE tm.ticket_id=:t ORDER BY tm.created_at ASC");
$movs->execute([':t'=>$ticket['id']]); $movs = $movs->fetchAll();
?>
<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <title>details - <?php echo e($protocolo); ?> - <?php echo e(app_name()); ?></title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="<?php echo base_url('assets/css/style.css'); ?>">
</head>
<body>
  <header class="navbar">
    <div class="container">
      <h1>Solicitação <?php echo e($protocolo); ?></h1>
      <div class="actions">
        <label style="display:flex;align-items:center;gap:6px;cursor:pointer;"><input type="checkbox" id="darkToggle"> Modo escuro</label>
        <a class="btn" href="<?php echo base_url('users/myrequest.php?matricula='.urlencode($ticket['matricula'])); ?>">Voltar</a>
      </div>
    </div>
  </header>
  <div class="container">
    <div class="grid cols-2">
      <div class="card">
        <h3>Dados da Solicitação</h3>
        <p><strong>users:</strong> <?php echo e($ticket['users_nome']); ?> (<?php echo e($ticket['matricula']); ?>)</p>
        <p><strong>Cargo:</strong> <?php echo e($ticket['cargo']); ?> <?php if ($ticket['curso']) echo ' | Curso: '.e($ticket['curso']); ?></p>
        <p><strong>Local:</strong> <?php echo e($ticket['local_problema']); ?></p>
        <p><strong>Categoria/Setor:</strong> <?php echo e($ticket['tipo_nome']); ?> / <?php echo e($ticket['setor_nome']); ?></p>
        <p><strong>Prioridade:</strong> <?php echo e($ticket['prioridade']); ?></p>
        <p><strong>Status:</strong> <?php echo e($ticket['status_nome']); ?></p>
        <p><strong>Aberto em:</strong> <?php echo e(date('d/m/Y H:i', strtotime($ticket['opened_at']))); ?></p>
        <p><strong>Última atualização:</strong> <?php echo e(date('d/m/Y H:i', strtotime($ticket['updated_at']))); ?></p>
        <p><strong>Descrição:</strong><br><?php echo nl2br(e($ticket['descricao'])); ?></p>
        <?php if ($ticket['image_path']): ?>
          <p><strong>Imagem:</strong><br><img src="<?php echo base_url($ticket['image_path']); ?>" style="max-width:100%;border:1px solid var(--border);border-radius:8px;"></p>
        <?php endif; ?>
      </div>
      <div class="card">
        <h3>Histórico</h3>
        <?php if (!$movs): ?>
          <p>Sem movimentações.</p>
        <?php else: ?>
          <ul>
            <?php foreach ($movs as $m): ?>
              <li style="margin-bottom:10px;">
                <strong><?php echo e($m['status_nome']); ?></strong>
                <small class="text-muted">em <?php echo e(date('d/m/Y H:i', strtotime($m['created_at']))); ?></small>
                <?php if ($m['admin_nome']): ?><small class="text-muted"> por <?php echo e($m['admin_nome']); ?></small><?php endif; ?>
                <?php if (!empty($m['resposta'])): ?><div><?php echo nl2br(e($m['resposta'])); ?></div><?php endif; ?>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>
      </div>
    </div>
  </div>
  <script src="<?php echo base_url('assets/js/app.js'); ?>"></script>
</body>
</html>
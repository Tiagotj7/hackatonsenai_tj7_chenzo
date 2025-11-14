<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../config/auth.php';
require_admin();

$kpiTotal = (int)$pdo->query("SELECT COUNT(*) FROM tickets")->fetchColumn();
$kpiAberta = (int)$pdo->query("SELECT COUNT(*) FROM tickets WHERE status_id=1")->fetchColumn();
$kpiAnd = (int)$pdo->query("SELECT COUNT(*) FROM tickets WHERE status_id=2")->fetchColumn();
$kpiConc = (int)$pdo->query("SELECT COUNT(*) FROM tickets WHERE status_id=3")->fetchColumn();

$prio = $pdo->query("SELECT prioridade, COUNT(*) c FROM tickets GROUP BY prioridade")->fetchAll();
$prioMap = ['Urgente'=>0,'Média'=>0,'Baixa'=>0]; foreach ($prio as $p) $prioMap[$p['prioridade']] = (int)$p['c'];

$cats = $pdo->query("SELECT rt.nome AS categoria, COUNT(*) c
                     FROM tickets t JOIN request_types rt ON rt.id=t.tipo_id
                     GROUP BY rt.id ORDER BY c DESC LIMIT 5")->fetchAll();

$pageTitle = 'Dashboard';
require __DIR__ . '/header.php';
?>
<div class="grid cols-4">
  <div class="card kpi"><div class="value"><?php echo $kpiTotal; ?></div><div class="label">Total</div></div>
  <div class="card kpi"><div class="value"><?php echo $kpiAberta; ?></div><div class="label">Abertas</div></div>
  <div class="card kpi"><div class="value"><?php echo $kpiAnd; ?></div><div class="label">Em andamento</div></div>
  <div class="card kpi"><div class="value"><?php echo $kpiConc; ?></div><div class="label">Concluídas</div></div>
</div>

<div class="grid cols-2">
  <div class="card">
    <h3>Distribuição por Prioridade</h3>
    <?php foreach ($prioMap as $label => $val):
      $perc = $kpiTotal > 0 ? round(($val/$kpiTotal)*100) : 0; ?>
      <div style="margin-bottom:10px;">
        <div style="display:flex;justify-content:space-between;">
          <span><?php echo e($label); ?></span><span><?php echo $val; ?> (<?php echo $perc; ?>%)</span>
        </div>
        <div class="progress-bar"><div class="fill" style="width: <?php echo $perc; ?>%"></div></div>
      </div>
    <?php endforeach; ?>
  </div>
  <div class="card">
    <h3>Categorias mais demandadas</h3>
    <?php if (!$cats): ?>
      <p>Nenhum dado.</p>
    <?php else: ?>
      <ul>
        <?php foreach ($cats as $c): ?>
          <li style="display:flex;justify-content:space-between;">
            <span><?php echo e($c['categoria']); ?></span><strong><?php echo (int)$c['c']; ?></strong>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  </div>
</div>

<div class="card">
  <a class="btn primary" href="<?php echo base_url('admin/solicitacao.php'); ?>">Ir para Tickets</a>
  <a class="btn" href="<?php echo base_url('admin/relatorios.php'); ?>">Relatórios</a>
</div>

<?php require __DIR__ . '/footer.php'; ?>
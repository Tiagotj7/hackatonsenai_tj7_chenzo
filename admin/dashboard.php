<?php
// admin/dashboard.php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../config/auth.php';
require_admin();

// KPIs
$kpiTotal  = (int)$pdo->query("SELECT COUNT(*) FROM tickets")->fetchColumn();
$kpiAberta = (int)$pdo->query("SELECT COUNT(*) FROM tickets WHERE status_id=1")->fetchColumn();
$kpiAnd    = (int)$pdo->query("SELECT COUNT(*) FROM tickets WHERE status_id=2")->fetchColumn();
$kpiConc   = (int)$pdo->query("SELECT COUNT(*) FROM tickets WHERE status_id=3")->fetchColumn();

// Distribuição por prioridade
$prio = $pdo->query("SELECT prioridade, COUNT(*) c FROM tickets GROUP BY prioridade")->fetchAll();
$prioMap = ['Urgente'=>0,'Média'=>0,'Baixa'=>0];
foreach ($prio as $p) $prioMap[$p['prioridade']] = (int)$p['c'];

// Top 5 categorias
$cats = $pdo->query("
  SELECT rt.nome AS categoria, COUNT(*) c
  FROM tickets t
  JOIN request_types rt ON rt.id = t.tipo_id
  GROUP BY rt.id
  ORDER BY c DESC
  LIMIT 5
")->fetchAll();

// Mapa por setor (abertas/andamento/concluídas/total)
$setorResumo = $pdo->query("
  SELECT s.nome AS setor,
         SUM(t.status_id=1) AS abertas,
         SUM(t.status_id=2) AS andamento,
         SUM(t.status_id=3) AS concluidas,
         COUNT(*) AS total
  FROM tickets t
  JOIN sectors s ON s.id = t.setor_id
  GROUP BY s.id
  ORDER BY total DESC
")->fetchAll();

// Dados dos gráficos
$chartStatusLabels = ['Abertas','Em andamento','Concluídas'];
$chartStatusData   = [$kpiAberta, $kpiAnd, $kpiConc];
$chartCatLabels    = array_map(fn($r)=>$r['categoria'], $cats);
$chartCatData      = array_map(fn($r)=>(int)$r['c'], $cats);

$pageTitle = 'Dashboard';
require __DIR__ . '/header.php';
?>
<div class="grid cols-4">
  <div class="card kpi">
    <div class="value"><?php echo $kpiTotal; ?></div>
    <div class="label">Total</div>
  </div>
  <div class="card kpi">
    <div class="value"><?php echo $kpiAberta; ?></div>
    <div class="label">Abertas</div>
  </div>
  <div class="card kpi">
    <div class="value"><?php echo $kpiAnd; ?></div>
    <div class="label">Em andamento</div>
  </div>
  <div class="card kpi">
    <div class="value"><?php echo $kpiConc; ?></div>
    <div class="label">Concluídas</div>
  </div>
</div>

<div class="grid cols-2">
  <div class="card">
    <h3>Distribuição por Prioridade</h3>
    <?php foreach ($prioMap as $label => $val):
      $perc = ($kpiTotal > 0) ? round(($val / $kpiTotal) * 100) : 0; ?>
      <div style="margin-bottom:10px;">
        <div style="display:flex;justify-content:space-between;">
          <span><?php echo e($label); ?></span>
          <span><?php echo $val; ?> (<?php echo $perc; ?>%)</span>
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
            <span><?php echo e($c['categoria']); ?></span>
            <strong><?php echo (int)$c['c']; ?></strong>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  </div>
</div>

<!-- Gráficos -->
<div class="grid cols-2">
  <div class="card">
    <h3>Chamados por Status</h3>
    <div style="height:280px;"><canvas id="chartStatus"></canvas></div>
  </div>
  <div class="card">
    <h3>Top Categorias</h3>
    <div style="height:280px;"><canvas id="chartCategorias"></canvas></div>
  </div>
</div>

<div class="card">
  <h3>Mapa por Setor</h3>
  <?php if (!$setorResumo): ?>
    <p>Nenhum dado.</p>
  <?php else: ?>
    <div class="table-responsive">
      <table class="table mobile-map table-compact">
        <thead>
          <tr>
            <th>Setor</th>
            <th>Abertas</th>
            <th>Em andamento</th>
            <th>Concluídas</th>
            <th>Total</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($setorResumo as $row): ?>
            <tr>
              <td><?php echo e($row['setor']); ?></td>
              <td><?php echo (int)$row['abertas']; ?></td>
              <td><?php echo (int)$row['andamento']; ?></td>
              <td><?php echo (int)$row['concluidas']; ?></td>
              <td><strong><?php echo (int)$row['total']; ?></strong></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>

<div class="card">
  <a class="btn primary" href="<?php echo base_url('admin/request.php'); ?>">Ir para Tickets</a>
  <a class="btn" href="<?php echo base_url('admin/relatorios.php'); ?>">Relatórios</a>
</div>

<!-- Chart.js local (baixe chart.umd.min.js e salve como assets/js/chart.min.js) -->
<script src="<?php echo base_url('assets/js/chart.min.js'); ?>"></script>
<script>
(function(){
  const statusLabels = <?php echo json_encode($chartStatusLabels, JSON_UNESCAPED_UNICODE); ?>;
  const statusData   = <?php echo json_encode($chartStatusData); ?>;
  const catLabels    = <?php echo json_encode($chartCatLabels, JSON_UNESCAPED_UNICODE); ?>;
  const catData      = <?php echo json_encode($chartCatData); ?>;

  let chartStatus, chartCats;

  function cssVar(name) {
    return getComputedStyle(document.documentElement).getPropertyValue(name).trim() || '#ccc';
  }

  function makeStatusChart(ctx) {
    const colors = [cssVar('--warning'), cssVar('--info'), cssVar('--success')];
    return new Chart(ctx, {
      type: 'doughnut',
      data: { labels: statusLabels, datasets: [{ data: statusData, backgroundColor: colors, borderColor: colors, borderWidth: 1 }] },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { position: 'bottom', labels: { color: cssVar('--text') } } }
      }
    });
  }

  function makeCatsChart(ctx) {
    const primary = cssVar('--primary');
    return new Chart(ctx, {
      type: 'bar',
      data: { labels: catLabels, datasets: [{ label: 'Quantidade', data: catData, backgroundColor: primary+'cc', borderColor: primary, borderWidth: 1, borderRadius: 6, maxBarThickness: 42 }] },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          x: { ticks: { color: cssVar('--text') }, grid: { color: cssVar('--border') } },
          y: { beginAtZero: true, ticks: { color: cssVar('--text') }, grid: { color: cssVar('--border') } }
        },
        plugins: { legend: { display: false } }
      }
    });
  }

  function renderCharts() {
    const c1 = document.getElementById('chartStatus');
    const c2 = document.getElementById('chartCategorias');
    if (chartStatus) chartStatus.destroy();
    if (chartCats) chartCats.destroy();
    if (c1) chartStatus = makeStatusChart(c1.getContext('2d'));
    if (c2) chartCats   = makeCatsChart(c2.getContext('2d'));
  }

  // Render inicial
  renderCharts();

  // Re-render ao alternar dark mode
  const toggle = document.getElementById('darkToggle');
  if (toggle) toggle.addEventListener('change', () => setTimeout(renderCharts, 50));

  // Re-render no resize
  window.addEventListener('resize', () => {
    clearTimeout(window.__chartResizeTimer);
    window.__chartResizeTimer = setTimeout(renderCharts, 200);
  });
})();
</script>

<?php require __DIR__ . '/footer.php'; ?>
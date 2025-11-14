<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../config/auth.php';
require_admin();

// KPIs e contagens
$kpiTotal = (int)$pdo->query("SELECT COUNT(*) FROM tickets")->fetchColumn();
$kpiAberta = (int)$pdo->query("SELECT COUNT(*) FROM tickets WHERE status_id=1")->fetchColumn();
$kpiAnd = (int)$pdo->query("SELECT COUNT(*) FROM tickets WHERE status_id=2")->fetchColumn();
$kpiConc = (int)$pdo->query("SELECT COUNT(*) FROM tickets WHERE status_id=3")->fetchColumn();

// Distribuição por prioridade
$prio = $pdo->query("SELECT prioridade, COUNT(*) c FROM tickets GROUP BY prioridade")->fetchAll();
$prioMap = ['Urgente'=>0,'Média'=>0,'Baixa'=>0];
foreach ($prio as $p) $prioMap[$p['prioridade']] = (int)$p['c'];

// Categorias mais demandadas (Top 5)
$cats = $pdo->query("SELECT rt.nome AS categoria, COUNT(*) c
                     FROM tickets t
                     JOIN request_types rt ON rt.id = t.tipo_id
                     GROUP BY rt.id
                     ORDER BY c DESC
                     LIMIT 5")->fetchAll();

// Dados para os gráficos (status e categorias)
$chartStatusLabels = ['Abertas','Em andamento','Concluídas'];
$chartStatusData = [$kpiAberta, $kpiAnd, $kpiConc];

$chartCatLabels = array_map(fn($r)=>$r['categoria'], $cats);
$chartCatData   = array_map(fn($r)=>(int)$r['c'], $cats);

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
      $perc = ($kpiTotal > 0) ? round(($val / $kpiTotal) * 100) : 0; ?>
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
            <span><?php echo e($c['categoria']); ?></span>
            <strong><?php echo (int)$c['c']; ?></strong>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  </div>
</div>

<!-- NOVO: Gráficos -->
<div class="grid cols-2">
  <div class="card">
    <h3>Chamados por Status</h3>
    <div style="height:280px;">
      <canvas id="chartStatus"></canvas>
    </div>
  </div>
  <div class="card">
    <h3>Top Categorias</h3>
    <div style="height:280px;">
      <canvas id="chartCategorias"></canvas>
    </div>
  </div>
</div>

<div class="card">
  <a class="btn primary" href="<?php echo base_url('admin/request.php'); ?>">Ir para Tickets</a>
  <a class="btn" href="<?php echo base_url('admin/relatorios.php'); ?>">Relatórios</a>
</div>

<!-- Chart.js CDN + script dos gráficos -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
(function(){
  // Dados vindos do PHP
  const statusLabels = <?php echo json_encode($chartStatusLabels, JSON_UNESCAPED_UNICODE); ?>;
  const statusData   = <?php echo json_encode($chartStatusData); ?>;
  const catLabels    = <?php echo json_encode($chartCatLabels, JSON_UNESCAPED_UNICODE); ?>;
  const catData      = <?php echo json_encode($chartCatData); ?>;

  let chartStatus, chartCats;

  function cssVar(name) {
    return getComputedStyle(document.documentElement).getPropertyValue(name).trim() || '#ccc';
    // ex.: cssVar('--primary')
  }

  function makeStatusChart(ctx) {
    const bg = [
      cssVar('--warning'), // abertas
      cssVar('--info'),    // em andamento
      cssVar('--success')  // concluídas
    ];
    const border = bg.map(c => c);
    return new Chart(ctx, {
      type: 'doughnut',
      data: {
        labels: statusLabels,
        datasets: [{
          data: statusData,
          backgroundColor: bg,
          borderColor: border,
          borderWidth: 1
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { position: 'bottom', labels: { color: cssVar('--text') } },
          tooltip: { enabled: true }
        }
      }
    });
  }

  function makeCatsChart(ctx) {
    const primary = cssVar('--primary');
    return new Chart(ctx, {
      type: 'bar',
      data: {
        labels: catLabels,
        datasets: [{
          label: 'Quantidade',
          data: catData,
          backgroundColor: primary + 'cc',
          borderColor: primary,
          borderWidth: 1,
          borderRadius: 6,
          maxBarThickness: 42
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          x: {
            ticks: { color: cssVar('--text') },
            grid: { color: cssVar('--border') }
          },
          y: {
            beginAtZero: true,
            ticks: { color: cssVar('--text') },
            grid: { color: cssVar('--border') }
          }
        },
        plugins: {
          legend: { display: false },
          tooltip: { enabled: true }
        }
      }
    });
  }

  function renderCharts() {
    const ctxStatus = document.getElementById('chartStatus');
    const ctxCats   = document.getElementById('chartCategorias');
    if (chartStatus) chartStatus.destroy();
    if (chartCats) chartCats.destroy();
    if (ctxStatus) chartStatus = makeStatusChart(ctxStatus.getContext('2d'));
    if (ctxCats)   chartCats   = makeCatsChart(ctxCats.getContext('2d'));
  }

  // Render inicial
  renderCharts();

  // Re-render quando alternar dark mode
  const toggle = document.getElementById('darkToggle');
  if (toggle) toggle.addEventListener('change', () => {
    setTimeout(renderCharts, 50);
  });

  // Re-render no resize (para ajustar labels)
  window.addEventListener('resize', () => {
    clearTimeout(window.__chartResizeTimer);
    window.__chartResizeTimer = setTimeout(renderCharts, 200);
  });
})();
</script>

<?php require __DIR__ . '/footer.php'; ?>
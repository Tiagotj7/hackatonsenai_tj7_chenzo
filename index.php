<?php
// index.php (Landing page profissional)
require_once __DIR__ . '/config/helpers.php';
?>
<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <meta name="robots" content="noindex,nofollow">
  <title><?php echo e(app_name()); ?> - Sistema de Chamados Internos</title>
  <link rel="stylesheet" href="<?php echo base_url('assets/css/style.css'); ?>">
  <style>
    /* Complemento visual exclusivo desta página */
    .hero {
      position: relative;
      background: radial-gradient(1200px 600px at 10% 10%, rgba(13,110,253,.15), transparent 60%),
                  radial-gradient(800px 400px at 90% 20%, rgba(25,135,84,.12), transparent 60%),
                  linear-gradient(180deg, var(--bg) 0%, var(--card) 100%);
      padding: 52px 0 36px;
      overflow: hidden;
    }
    .hero .container {
      display: grid;
      grid-template-columns: 1.1fr .9fr;
      gap: 24px;
      align-items: center;
    }
    @media (max-width: 900px) {
      .hero .container { grid-template-columns: 1fr; }
    }
    .pill {
      display: inline-flex; align-items: center; gap: 8px;
      padding: 6px 10px; border: 1px dashed var(--border);
      background: rgba(13,110,253,.08); color: var(--primary);
      border-radius: 999px; font-size: 12px; font-weight: 600;
    }
    .hero h1 {
      font-size: clamp(28px, 4vw, 44px);
      line-height: 1.15; margin: 10px 0 8px;
    }
    .hero p.lead {
      color: var(--muted);
      font-size: clamp(14px, 2.2vw, 18px);
      margin: 0 0 18px;
    }
    .hero .cta { display: flex; gap: 10px; flex-wrap: wrap; }
    .glass {
      background: rgba(255,255,255,.5);
      border: 1px solid var(--border);
      backdrop-filter: blur(6px);
      border-radius: 14px;
      padding: 14px;
    }
    .dark .glass { background: rgba(23,25,35,.35); }

    .i-grid { display: grid; gap: 16px; }
    .i-grid.cols-3 { grid-template-columns: repeat(3,1fr); }
    @media (max-width: 900px) { .i-grid.cols-3 { grid-template-columns: 1fr; } }

    .feature {
      display: flex; gap: 12px; align-items: flex-start;
      border: 1px solid var(--border); border-radius: 12px; padding: 14px;
      background: var(--card);
    }
    .icon-circle {
      width: 40px; height: 40px; border-radius: 50%;
      display: grid; place-items: center;
      background: rgba(13,110,253,.12); color: var(--primary);
      flex: 0 0 40px;
    }
    .feature h4 { margin: 2px 0 4px; font-size: 16px; }
    .feature p { margin: 0; color: var(--muted); font-size: 14px; }

    .steps { position: relative; }
    .steps .lane {
      position: absolute; left: 20px; top: 0; bottom: 0;
      width: 2px; background: var(--border);
    }
    .step {
      display: grid; grid-template-columns: 40px 1fr; gap: 12px;
      align-items: start; margin-bottom: 14px;
    }
    .step .num {
      width: 40px; height: 40px; border-radius: 50%;
      background: var(--primary); color: #fff; font-weight: 700;
      display: grid; place-items: center; border: 2px solid rgba(255,255,255,.6);
      box-shadow: 0 6px 20px rgba(13,110,253,.3);
      z-index: 1;
    }
    .step .body h5 { margin: 2px 0 4px; }
    .step .body p { margin: 0; color: var(--muted); }

    .quick {
      display: grid; gap: 16px; grid-template-columns: 1fr 1fr;
    }
    @media (max-width: 900px) { .quick { grid-template-columns: 1fr; } }
    .quick .card h3 { margin-top: 0; }

    .mini-muted { color: var(--muted); font-size: 12px; }

    .footer-links {
      display: flex; gap: 12px; justify-content: center; flex-wrap: wrap;
      margin-top: 10px;
    }
  </style>
</head>
<body>
  <header class="navbar">
    <div class="container">
      <h1 style="display:flex;align-items:center;gap:8px;">
        <span style="display:inline-grid;place-items:center;width:28px;height:28px;border-radius:6px;background:rgba(13,110,253,.15);color:var(--primary);font-weight:700;">S</span>
        <?php echo e(app_name()); ?>
      </h1>
      <div class="actions">
        <label style="display:flex;align-items:center;gap:6px;cursor:pointer;">
          <input type="checkbox" id="darkToggle"> Modo escuro
        </label>
        <a class="btn" href="<?php echo base_url('admin/login.php'); ?>">Admin</a>
      </div>
    </div>
  </header>

  <main>
    <!-- HERO -->
    <section class="hero">
      <div class="container">
        <div>
          <span class="pill">Sistema Interno de TI e Manutenção</span>
          <h1>Centralize, priorize e resolva solicitações com eficiência</h1>
          <p class="lead">Professores e colaboradores abrem chamados em minutos. Os setores acompanham, respondem e concluem com histórico, filtros e relatórios.</p>
          <div class="cta">
            <a class="btn primary" href="<?php echo base_url('solicitante/nova.php'); ?>">Abrir Solicitação</a>
            <a class="btn" href="<?php echo base_url('solicitante/minhas.php'); ?>">Acompanhar Minhas</a>
            <a class="btn success" href="<?php echo base_url('admin/login.php'); ?>">Entrar no Painel</a>
          </div>
          <div class="mini-muted" style="margin-top:8px;">Sem cadastro para solicitante • Protocolo e matrícula para acompanhar</div>
        </div>
        <div class="glass">
          <div class="i-grid cols-3">
            <div class="feature">
              <div class="icon-circle">⚡</div>
              <div>
                <h4>Registro ágil</h4>
                <p>Campos essenciais e upload de imagem.</p>
              </div>
            </div>
            <div class="feature">
              <div class="icon-circle">📊</div>
              <div>
                <h4>Dashboard e filtros</h4>
                <p>Por setor, prioridade, período e status.</p>
              </div>
            </div>
            <div class="feature">
              <div class="icon-circle">🔒</div>
              <div>
                <h4>Acesso seguro</h4>
                <p>Admins com sessão e permissões.</p>
              </div>
            </div>
            <div class="feature">
              <div class="icon-circle">🧾</div>
              <div>
                <h4>Relatórios</h4>
                <p>Exportação CSV para auditoria.</p>
              </div>
            </div>
            <div class="feature">
              <div class="icon-circle">🖼️</div>
              <div>
                <h4>Evidências</h4>
                <p>Envio de imagem do problema.</p>
              </div>
            </div>
            <div class="feature">
              <div class="icon-circle">🌙</div>
              <div>
                <h4>Dark Mode</h4>
                <p>Conforto visual imediato.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- AÇÕES RÁPIDAS -->
    <section class="container">
      <?php foreach (get_flash() as $f): ?>
        <div class="alert <?php echo e($f['type']); ?>"><?php echo e($f['message']); ?></div>
      <?php endforeach; ?>

      <div class="quick">
        <div class="card">
          <h3>Buscar Minhas Solicitações</h3>
          <p class="mini-muted">Informe sua matrícula para visualizar status, respostas e histórico.</p>
          <form method="get" action="<?php echo base_url('solicitante/minhas.php'); ?>" class="grid cols-2" novalidate>
            <div class="form-group" style="grid-column:1/-1;">
              <label>Matrícula</label>
              <input type="text" name="matricula" placeholder="Ex.: 20231234" required>
            </div>
            <div class="form-group" style="grid-column:1/-1;">
              <button class="btn primary" type="submit">Consultar</button>
              <a class="btn" href="<?php echo base_url('solicitante/nova.php'); ?>">Abrir nova solicitação</a>
            </div>
          </form>
        </div>

        <div class="card">
          <h3>Como funciona</h3>
          <div class="steps" style="position:relative; padding-left:8px;">
            <div class="lane"></div>
            <div class="step">
              <div class="num">1</div>
              <div class="body">
                <h5>Abra o chamado</h5>
                <p>Informe local, categoria, prioridade e descreva o problema.</p>
              </div>
            </div>
            <div class="step">
              <div class="num">2</div>
              <div class="body">
                <h5>Acompanhe o andamento</h5>
                <p>Veja atualizações do setor e receba retorno pelo histórico.</p>
              </div>
            </div>
            <div class="step">
              <div class="num">3</div>
              <div class="body">
                <h5>Concluído e registrado</h5>
                <p>Chamado finalizado com rastreabilidade e métricas.</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- CTA FINAL -->
      <div class="card" style="display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;">
        <div>
          <strong>Pronto para organizar seus chamados?</strong>
          <div class="mini-muted">Ganhe visibilidade do que está aberto, em andamento e concluído.</div>
        </div>
        <div style="display:flex;gap:8px;">
          <a class="btn primary" href="<?php echo base_url('solicitante/nova.php'); ?>">Abrir Solicitação</a>
          <a class="btn success" href="<?php echo base_url('admin/login.php'); ?>">Ir para o Painel</a>
        </div>
      </div>
    </section>
  </main>

  <footer class="footer">
    <div>© <?php echo date('Y'); ?> <?php echo e(app_name()); ?> — SENAI</div>
    <div class="footer-links">
      <a href="<?php echo base_url('solicitante/nova.php'); ?>">Abrir Solicitação</a>
      <a href="<?php echo base_url('solicitante/minhas.php'); ?>">Minhas Solicitações</a>
      <a href="<?php echo base_url('admin/login.php'); ?>">Acesso Admin</a>
    </div>
  </footer>

  <script src="<?php echo base_url('assets/js/app.js'); ?>"></script>
</body>
</html>
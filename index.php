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
        </section>
    </main>
    <section class="access">
        <div>© <?php echo date('Y'); ?> <?php echo e(app_name()); ?> — SENAI</div>
        <div class="footer-links">
            <a href="<?php echo base_url('solicitante/nova.php'); ?>">Abrir Solicitação</a>
            <a href="<?php echo base_url('admin/login.php'); ?>">Acesso Admin</a>
        </div>
    </section>


    <script src="<?php echo base_url('assets/js/app.js'); ?>"></script>
</body>

</html>
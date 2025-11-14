<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/helpers.php';
$APP = function_exists('app_name') ? app_name() : (defined('APP_NAME') ? APP_NAME : 'Senai Service Manager');
?>
<!doctype html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <title><?php echo e($APP); ?> - Sistema de Chamados Internos</title>
    <link rel="stylesheet" href="<?php echo base_url('assets/css/style.css'); ?>">
</head>

<body>
    <header class="navbar">
        <div class="container">
            <h1>
                <span style="display:inline-grid;place-items:center;width:28px;height:28px;border-radius:6px;background:rgba(37,99,235,.15);color:var(--primary);font-weight:700;">S</span>
                <?php echo e($APP); ?>
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
        <section class="hero">
            <div class="container">
                <div>
                    <span class="pill">Sistema Interno de TI e Manutenção</span>
                    <h1>Centralize, priorize e resolva solicitações com eficiência</h1>
                    <p class="lead">Professores e colaboradores abrem chamados em minutos. Os setores acompanham, respondem e concluem com histórico, filtros e relatórios.</p>
                    <div class="cta">
                        <a class="btn primary" href="<?php echo base_url('users/create.php'); ?>">Abrir Solicitação</a>
                        <a class="btn outline primary" href="<?php echo base_url('users/myrequest.php'); ?>">Acompanhar Minhas</a>
                        <a class="btn success" href="<?php echo base_url('users/interface.php'); ?>">Entrar no Painel</a>
                    </div>
                    <div class="mini-muted" style="margin-top:8px;">Sem cadastro para users • Use matrícula para acompanhar</div>
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

        <section class="container">
            <?php foreach (get_flash() as $f): ?>
                <div class="alert <?php echo e($f['type']); ?>"><?php echo e($f['message']); ?></div>
            <?php endforeach; ?>

            <div class="quick">
                <div class="card">
                    <div class="card-header">
                        <h3>Buscar Minhas Solicitações</h3>
                    </div>
                    <p class="mini-muted">Informe sua matrícula para visualizar status, respostas e histórico.</p>
                    <form method="get" action="<?php echo base_url('users/myrequest.php'); ?>" class="grid cols-2" novalidate>
                        <div class="form-group" style="grid-column:1/-1;">
                            <label>Matrícula</label>
                            <input type="text" name="matricula" placeholder="Ex.: 20231234" required>
                        </div>
                        <div class="form-group" style="grid-column:1/-1;display:flex;gap:8px;flex-wrap:wrap;">
                            <button class="btn primary" type="submit">Consultar</button>
                            <a class="btn" href="<?php echo base_url('users/create.php'); ?>">Abrir nova solicitação</a>
                        </div>
                    </form>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3>Como funciona</h3>
                    </div>
                    <div class="steps">
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
                                <p>Veja atualizações do setor e o histórico de respostas.</p>
                            </div>
                        </div>
                        <div class="step">
                            <div class="num">3</div>
                            <div class="body">
                                <h5>Concluído e registrado</h5>
                                <p>Finalização com rastreabilidade e métricas no painel.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card" style="display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;">
                <div>
                    <strong>Pronto para organizar seus chamados?</strong>
                    <div class="mini-muted">Visibilidade completa do que está aberto, em andamento e concluído.</div>
                </div>
                <div style="display:flex;gap:8px;flex-wrap:wrap;">
                    <a class="btn primary" href="<?php echo base_url('users/create.php'); ?>">Abrir Solicitação</a>
                    <a class="btn success" href="<?php echo base_url('admin/login.php'); ?>">Ir para o Painel</a>
                </div>
            </div>
        </section>
    </main>

    <footer class="footer">
        <div>© <?php echo date('Y'); ?> <?php echo e($APP); ?> — SENAI</div>
        <div class="footer-links">
            <a href="<?php echo base_url('users/create.php'); ?>">Abrir Solicitação</a>
            <a href="<?php echo base_url('users/myrequest.php'); ?>">Minhas Solicitações</a>
            <a href="<?php echo base_url('admin/login.php'); ?>">Acesso Admin</a>
        </div>
    </footer>

    <script src="<?php echo base_url('assets/js/app.js'); ?>"></script>
</body>

</html>

<?php
// DEBUG TEMPORÁRIO — REMOVA APÓS USAR
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h2>PHP info</h2>";
phpinfo();

// Teste rápido de conexão com o banco (preencha com suas credenciais ou use variáveis de ambiente)
$host = getenv('DB_HOST') ?: 'sql212.infinityfree.com';
$db   = getenv('DB_NAME') ?: 'if0_40352073_db_agendeaqui';
$user = getenv('DB_USER') ?: 'if0_40352073';
$pass = getenv('DB_PASS') ?: 'xldkrDW2IYPMMuH';
$charset = 'utf8mb4';

echo "<h2>Teste DB</h2>";
$dsn = "mysql:host={$host};dbname={$db};charset={$charset}";
try {
    $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    echo "Conexão com o DB OK.";
} catch (PDOException $e) {
    echo "Erro ao conectar no DB: " . htmlspecialchars($e->getMessage());
}
?>
<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/helpers.php';
$APP = function_exists('app_name') ? app_name() : (defined('APP_NAME') ? APP_NAME : 'Senai Service Manager');
?>
<!DOCTYPE html>
<html lang="pt-br">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <meta name="robots" content="noindex,nofollow" />
    <title>Senai Service Manager</title>
    <link rel="stylesheet" href="/assets/css/style.css" />
    <link rel="stylesheet" href="/assets/css/index.css" />
    <link rel="shortcut icon" href="<?php echo base_url('assets/images/favicon.ico'); ?>" type="image/x-icon">

  </head>

  <body>
    <header>
      <div class="container">
        <h1>Senai <span>Service Manager</span></h1>
        <div class="actions">
          <label> <input type="checkbox" id="darkToggle" /> Modo escuro </label>
          <a class="btn" href="admin/login.php">Admin</a>
        </div>
      </div>
    </header>

    <main>
      <section class="hero">
        <div class="container">
          <div class="container_text">
            <span class="pill">Sistema Interno de TI e Manutenção</span>
            <h1>Centralize, priorize e resolva solicitações com eficiência</h1>
            <p class="lead">
              Professores e colaboradores abrem chamados em minutos. Os setores
              acompanham, respondem e concluem com histórico, filtros e
              relatórios.
            </p>
            <h2>Acesse aqui o seu perfil</h2>
            <div class="links">
              <a href="users/interface.php">Solicitante</a>
              <a href="users/myrequest.php">Consultar</a>
            </div>
          </div>
          <div class="glass">
            <div class="feature">
              <div class="icon-circle">⚡</div>
              <div class="feature_text">
                <h4>Registro ágil</h4>
                <p>Campos essenciais e upload de imagem.</p>
              </div>
            </div>
            <div class="feature">
              <div class="icon-circle">📊</div>
              <div class="feature_text">
                <h4>Dashboard e filtros</h4>
                <p>Por setor, prioridade, período e status.</p>
              </div>
            </div>
            <div class="feature">
              <div class="icon-circle">🔒</div>
              <div class="feature_text">
                <h4>Acesso seguro</h4>
                <p>Admins com sessão e permissões.</p>
              </div>
            </div>
            <div class="feature">
              <div class="icon-circle">🧾</div>
              <div class="feature_text">
                <h4>Relatórios</h4>
                <p>Exportação CSV para auditoria.</p>
              </div>
            </div>
            <div class="feature">
              <div class="icon-circle">🖼️</div>
              <div class="feature_text">
                <h4>Evidências</h4>
                <p>Envio de imagem do problema.</p>
              </div>
            </div>
            <div class="feature">
              <div class="icon-circle">🌙</div>
              <div class="feature_text">
                <h4>Dark Mode</h4>
                <p>Conforto visual imediato.</p>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section class="card">
        <div class="card-header">
          <h2>Como funciona</h2>
        </div>
        <div class="steps">
          <div class="step">
            <div class="step_body">
              <h4>Abra o chamado</h4>
              <p>Informe local, categoria, prioridade e descreva o problema.</p>
            </div>
          </div>
          <div class="step">
            <div class="step_body">
              <h4>Acompanhe o andamento</h4>
              <p>Veja atualizações do setor e o histórico de respostas.</p>
            </div>
          </div>
          <div class="step">
            <div class="step_body">
              <h4>Concluído e registrado</h4>
              <p>Finalização com rastreabilidade e métricas no painel.</p>
            </div>
          </div>
        </div>
      </section>
    </main>

    <footer class="footer">
      <div>© 2025 Senai Service Manager — SENAI</div>
    </footer>
  </body>
  <script src="assets/js/app.js"></script>
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
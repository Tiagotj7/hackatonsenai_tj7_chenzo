<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/helpers.php';

$stmt = $pdo->query("SELECT COUNT(*) FROM users_admin");
$hasUsers = (int)$stmt->fetchColumn() > 0;

function is_password_hash_format($hash) {
  return (bool)preg_match('/^\$(2y|2a|2b|argon2id|argon2i)\$/', (string)$hash);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  verify_csrf();

  if (isset($_POST['seed_admin'])) {
    if ($hasUsers) {
      flash('warning', 'Já existe usuário cadastrado.');
    } else {
      $hash = password_hash('admin123', PASSWORD_BCRYPT);
      $ins = $pdo->prepare("INSERT INTO users_admin (nome, usuario, senha_hash, cargo, setor_id, ativo)
                            VALUES ('Administrador','admin',:h,'Administrador',1,1)");
      $ins->execute([':h' => $hash]);
      flash('success', 'Admin criado: admin / admin123');
      $hasUsers = true;
    }
    redirect('admin/login.php');
  }

  $usuario = trim($_POST['usuario'] ?? '');
  $senha   = $_POST['senha'] ?? '';

  $st = $pdo->prepare("SELECT * FROM users_admin WHERE usuario = :u AND ativo = 1");
  $st->execute([':u' => $usuario]);
  $user = $st->fetch();

  $ok = false;
  if ($user) {
    $hash = $user['senha_hash'];
    if (is_password_hash_format($hash)) $ok = password_verify($senha, $hash);
    else $ok = ($senha === $hash); // fallback
  }

  if ($ok) {
    $_SESSION['admin_user'] = [
      'id' => $user['id'], 'nome' => $user['nome'], 'usuario' => $user['usuario'],
      'setor_id' => $user['setor_id'], 'cargo' => $user['cargo']
    ];
    flash('success', 'Login realizado.');
    redirect('admin/dashboard.php');
  } else {
    flash('error', 'Usuário ou senha inválidos.');
  }
}

$pageTitle = 'Voltar';
require __DIR__ . '/header.php';
?>
<div class="card" style="max-width:480px;margin:0 auto;">
  <form method="post" novalidate>
    <input type="hidden" name="csrf" value="<?php echo csrf_token(); ?>">
    <div class="form-group"><label>Usuário</label><input type="text" name="usuario" required></div>
    <div class="form-group"><label>Senha</label><input type="password" name="senha" required></div>
    <button class="btn primary" type="submit">Entrar</button>
  </form>
</div>
<?php if (!$hasUsers): ?>
  <div class="card" style="max-width:480px;margin:12px auto;">
    <form method="post">
      <input type="hidden" name="csrf" value="<?php echo csrf_token(); ?>">
      <button class="btn" name="seed_admin" value="1">Criar admin padrão (admin / admin123)</button>
    </form>
  </div>
<?php endif; ?>
<?php require __DIR__ . '/footer.php'; ?>
<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/helpers.php';

// Se tabela vazia, exibe botão para criar admin padrão
$stmt = $pdo->query("SELECT COUNT(*) FROM users_admin");
$hasUsers = (int)$stmt->fetchColumn() > 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  verify_csrf();

  if (isset($_POST['seed_admin'])) {
    if ($hasUsers) {
      flash('warning', 'Já existe usuário cadastrado.');
    } else {
      $ins = $pdo->prepare("INSERT INTO users_admin (nome, usuario, senha_hash, cargo, setor_id, ativo)
                            VALUES ('Administrador','admin','admin123','Administrador',1,1)");
      $ins->execute();
      flash('success', 'Admin padrão criado: admin / admin123');
      $hasUsers = true;
    }
    redirect('admin/login.php');
  }

  $usuario = trim($_POST['usuario'] ?? '');
  $senha   = $_POST['senha'] ?? '';

  $stmt = $pdo->prepare("SELECT * FROM users_admin WHERE usuario = :u AND ativo = 1");
  $stmt->execute([':u' => $usuario]);
  $user = $stmt->fetch();

  $ok = false;
  if ($user) {
    $hash = $user['senha_hash'];
    if (str_starts_with($hash, '$2y$') || str_starts_with($hash, '$argon')) {
      $ok = password_verify($senha, $hash);
    } else {
      $ok = ($senha === $hash); // fallback para senha simples
    }
  }

  if ($ok) {
    $_SESSION['admin_user'] = [
      'id' => $user['id'],
      'nome' => $user['nome'],
      'usuario' => $user['usuario'],
      'setor_id' => $user['setor_id'],
      'cargo' => $user['cargo']
    ];
    flash('success', 'Login realizado com sucesso.');
    redirect('admin/dashboard.php');
  } else {
    flash('error', 'Usuário ou senha inválidos.');
  }
}

$pageTitle = 'Login';
require __DIR__ . '/header.php';
?>
<div class="card" style="max-width:480px;margin:0 auto;">
  <form method="post" createlidate>
    <input type="hidden" name="csrf" value="<?php echo csrf_token(); ?>">
    <div class="form-group">
      <label>Usuário</label>
      <input type="text" name="usuario" required>
    </div>
    <div class="form-group">
      <label>Senha</label>
      <input type="password" name="senha" required>
    </div>
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
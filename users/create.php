<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/helpers.php';

$tipos = $pdo->query("SELECT rt.id, rt.nome, s.nome AS setor FROM request_types rt
                      JOIN sectors s ON s.id=rt.setor_id
                      WHERE rt.ativo=1 AND s.ativo=1 ORDER BY rt.nome")->fetchAll();
?>
<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <title>Abrir Solicitação - <?php echo APP_NAME; ?></title>
  <link rel="stylesheet" href="<?php echo base_url('assets/css/style.css'); ?>">
</head>
<body>
  <nav class="navbar">
    <div class="container">
      <h1>Abrir Solicitação</h1>
      <div class="actions">
        <label style="display:flex;align-items:center;gap:6px;cursor:pointer;">
          <input type="checkbox" id="darkToggle"> Modo escuro
        </label>
        <a class="btn" href="<?php echo base_url('interface.php'); ?>">Início</a>
      </div>
    </div>
  </nav>
  <div class="container">
    <?php foreach (get_flash() as $f): ?>
      <div class="alert <?php echo e($f['type']); ?>"><?php echo e($f['message']); ?></div>
    <?php endforeach; ?>

    <div class="card">
      <form method="post" action="<?php echo base_url('users/salvar.php'); ?>" enctype="multipart/form-data" createlidate>
        <input type="hidden" name="csrf" value="<?php echo csrf_token(); ?>">
        <div class="grid cols-2">
          <div class="form-group">
            <label>Nome do users</label>
            <input type="text" name="users_nome" required>
          </div>
          <div class="form-group">
            <label>Matrícula</label>
            <input type="text" name="matricula" required>
          </div>
          <div class="form-group">
            <label>Cargo</label>
            <input type="text" name="cargo" placeholder="Professor, Técnico, Funcionário..." required>
          </div>
          <div class="form-group">
            <label>Curso (se aplicável)</label>
            <input type="text" name="curso" placeholder="Ex.: Eletromecânica">
          </div>
          <div class="form-group">
            <label>Local do problema</label>
            <input type="text" name="local_problema" placeholder="Sala 101, Lab Informática, Oficina..." required>
          </div>
          <div class="form-group">
            <label>Categoria</label>
            <select name="tipo_id" required>
              <option value="">Selecione...</option>
              <?php foreach ($tipos as $t): ?>
                <option value="<?php echo (int)$t['id']; ?>"><?php echo e($t['nome'].' ('.$t['setor'].')'); ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label>Prioridade</label>
            <select name="prioridade" required>
              <option value="">Selecione...</option>
              <option>Urgente</option><option>Média</option><option>Baixa</option>
            </select>
          </div>
          <div class="form-group">
            <label>E-mail (para notificação de conclusão - opcional)</label>
            <input type="email" name="email" placeholder="seunome@senai.br">
          </div>
        </div>
        <div class="form-group">
          <label>Descrição detalhada</label>
          <textarea name="descricao" required></textarea>
        </div>
        <div class="form-group">
          <label>Imagem (opcional, até <?php echo max_upload_mb(); ?>MB)</label>
          <input type="file" name="imagem" accept="image/*">
        </div>
        <button class="btn primary" type="submit">Enviar Solicitação</button>
      </form>
    </div>
  </div>
  <script src="<?php echo base_url('assets/js/app.js'); ?>"></script>
</body>
</html>
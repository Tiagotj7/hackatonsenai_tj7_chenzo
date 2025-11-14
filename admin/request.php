<?php
// admin/request.php - Tabela desktop + Grid mobile com ações empilhadas
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/mailer.php';
require_admin();

$statuses = $pdo->query("SELECT id,nome FROM ticket_status WHERE ativo=1 ORDER BY id")->fetchAll();
$tipos    = $pdo->query("SELECT id,nome FROM request_types WHERE ativo=1 ORDER BY nome")->fetchAll();
$setores  = $pdo->query("SELECT id,nome FROM sectors WHERE ativo=1 ORDER BY nome")->fetchAll();

function build_filters(&$params) {
  $where = [];
  if (!empty($_GET['tipo_id']))     { $where[]="t.tipo_id=:tipo_id";         $params[':tipo_id']=(int)$_GET['tipo_id']; }
  if (!empty($_GET['setor_id']))    { $where[]="t.setor_id=:setor_id";       $params[':setor_id']=(int)$_GET['setor_id']; }
  if (!empty($_GET['status_id']))   { $where[]="t.status_id=:status_id";     $params[':status_id']=(int)$_GET['status_id']; }
  if (!empty($_GET['prioridade']))  { $where[]="t.prioridade=:prioridade";   $params[':prioridade']=$_GET['prioridade']; }
  if (!empty($_GET['local']))       { $where[]="t.local_problema LIKE :local"; $params[':local']='%'.$_GET['local'].'%'; }
  if (!empty($_GET['curso']))       { $where[]="t.curso LIKE :curso";        $params[':curso']='%'.$_GET['curso'].'%'; }
  if (!empty($_GET['periodo_ini'])) { $where[]="DATE(t.opened_at)>=:ini";    $params[':ini']=$_GET['periodo_ini']; }
  if (!empty($_GET['periodo_fim'])) { $where[]="DATE(t.opened_at)<=:fim";    $params[':fim']=$_GET['periodo_fim']; }
  return $where ? (' WHERE ' . implode(' AND ', $where)) : '';
}

// Atualização (detalhe)
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['update_ticket'])) {
  verify_csrf();
  $id = (int)($_POST['id'] ?? 0);
  $new_status = (int)($_POST['status_id'] ?? 0);
  $resposta = trim($_POST['resposta'] ?? '');
  $pdo->beginTransaction();
  try {
    $pdo->prepare("UPDATE tickets SET status_id=:s WHERE id=:id")->execute([':s'=>$new_status, ':id'=>$id]);
    $pdo->prepare("INSERT INTO ticket_movements (ticket_id,user_id,status_id,resposta) VALUES (:t,:u,:s,:r)")
        ->execute([':t'=>$id, ':u'=>current_admin()['id'], ':s'=>$new_status, ':r'=>$resposta ?: null]);
    $pdo->prepare("UPDATE tickets SET updated_at=NOW() WHERE id=:id")->execute([':id'=>$id]);
    $t = $pdo->prepare("SELECT protocolo,email FROM tickets WHERE id=:id"); $t->execute([':id'=>$id]); $row=$t->fetch();
    $pdo->commit();
    if ($new_status===3 && EMAIL_ENABLED && !empty($row['email'])) {
      $body = "<p>Sua solicitação <strong>{$row['protocolo']}</strong> foi concluída.</p>";
      if ($resposta) $body .= "<p>Mensagem do setor: ".nl2br(e($resposta))."</p>";
      @send_email($row['email'], "[SENAI] Chamado {$row['protocolo']} Concluído", $body);
    }
    flash('success','Atualização salva.');
    redirect('admin/request.php?id='.$id);
  } catch(Exception $e) {
    $pdo->rollBack();
    flash('error','Erro: '.$e->getMessage());
    redirect('admin/request.php?id='.$id);
  }
}

// Ação rápida na lista
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['quick_update'])) {
  verify_csrf();
  $id = (int)($_POST['id'] ?? 0);
  $new_status = (int)($_POST['status_id'] ?? 0);
  $pdo->beginTransaction();
  try {
    $pdo->prepare("UPDATE tickets SET status_id=:s WHERE id=:id")->execute([':s'=>$new_status, ':id'=>$id]);
    $pdo->prepare("INSERT INTO ticket_movements (ticket_id,user_id,status_id,resposta) VALUES (:t,:u,:s,NULL)")
        ->execute([':t'=>$id, ':u'=>current_admin()['id'], ':s'=>$new_status]);
    $pdo->prepare("UPDATE tickets SET updated_at=NOW() WHERE id=:id")->execute([':id'=>$id]);
    if ($new_status===3 && EMAIL_ENABLED) {
      $t = $pdo->prepare("SELECT protocolo,email FROM tickets WHERE id=:id"); $t->execute([':id'=>$id]); $row=$t->fetch();
      if (!empty($row['email'])) @send_email($row['email'], "[SENAI] Chamado {$row['protocolo']} Concluído", "<p>Sua solicitação <strong>{$row['protocolo']}</strong> foi concluída.</p>");
    }
    $pdo->commit();
    flash('success','Status atualizado.');
  } catch(Exception $e) {
    $pdo->rollBack();
    flash('error','Erro: '.$e->getMessage());
  }
  redirect('admin/request.php');
}

$pageTitle = 'Tickets';
require __DIR__ . '/header.php';

if (isset($_GET['id'])):
  $id = (int)$_GET['id'];
  $sql = "SELECT t.*, rt.nome AS tipo_nome, s.nome AS setor_nome, ts.nome AS status_nome
          FROM tickets t
          JOIN request_types rt ON rt.id=t.tipo_id
          JOIN sectors s ON s.id=t.setor_id
          JOIN ticket_status ts ON ts.id=t.status_id
          WHERE t.id=:id";
  $st = $pdo->prepare($sql); $st->execute([':id'=>$id]); $ticket = $st->fetch();
  if (!$ticket) { flash('error','Ticket não encontrado.'); redirect('admin/request.php'); }

  $movs = $pdo->prepare("SELECT tm.*, ua.nome AS admin_nome, ts.nome AS status_nome
                         FROM ticket_movements tm
                         JOIN ticket_status ts ON ts.id=tm.status_id
                         LEFT JOIN users_admin ua ON ua.id=tm.user_id
                         WHERE tm.ticket_id=:t ORDER BY tm.created_at ASC");
  $movs->execute([':t'=>$id]); $movs = $movs->fetchAll();
?>
  <div class="grid cols-2">
    <div class="card">
      <h3>Ticket <?php echo e($ticket['protocolo']); ?></h3>
      <p><strong>Usuário:</strong> <?php echo e($ticket['users_nome']); ?> (<?php echo e($ticket['matricula']); ?>)</p>
      <p><strong>Cargo/Curso:</strong> <?php echo e($ticket['cargo']); ?> <?php if ($ticket['curso']) echo ' | ' . e($ticket['curso']); ?></p>
      <p><strong>Local:</strong> <?php echo e($ticket['local_problema']); ?></p>
      <p><strong>Categoria/Setor:</strong> <?php echo e($ticket['tipo_nome']); ?> / <?php echo e($ticket['setor_nome']); ?></p>
      <p><strong>Prioridade:</strong> <?php echo e($ticket['prioridade']); ?></p>
      <p><strong>Status:</strong> <?php echo e($ticket['status_nome']); ?></p>
      <p><strong>Abertura:</strong> <?php echo e(date('d/m/Y H:i', strtotime($ticket['opened_at']))); ?></p>
      <p><strong>Atualização:</strong> <?php echo e(date('d/m/Y H:i', strtotime($ticket['updated_at']))); ?></p>
      <p><strong>Descrição:</strong><br><?php echo nl2br(e($ticket['descricao'])); ?></p>
      <?php if ($ticket['image_path']): ?>
        <p><strong>Imagem:</strong><br><img src="<?php echo base_url($ticket['image_path']); ?>" style="max-width:100%;border:1px solid var(--border);border-radius:8px;"></p>
      <?php endif; ?>
    </div>
    <div class="card">
      <h3>Atualizar Status / Responder</h3>
      <form method="post" novalidate>
        <input type="hidden" name="csrf" value="<?php echo csrf_token(); ?>">
        <input type="hidden" name="id" value="<?php echo (int)$ticket['id']; ?>">
        <input type="hidden" name="update_ticket" value="1">
        <div class="form-group">
          <label>Status</label>
          <select name="status_id" required>
            <?php foreach ($statuses as $s): ?>
              <option value="<?php echo (int)$s['id']; ?>" <?php if ($s['id']==$ticket['status_id']) echo 'selected'; ?>>
                <?php echo e($s['nome']); ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label>Resposta/Andamento (opcional)</label>
          <textarea name="resposta" placeholder="Informe andamento, orientações ou conclusão..."></textarea>
        </div>
        <button class="btn primary" type="submit">Salvar</button>
      </form>
    </div>
  </div>
  <div class="card">
    <h3>Histórico</h3>
    <?php if (!$movs): ?><p>Sem histórico.</p>
    <?php else: ?>
      <div class="table-responsive"><table class="table">
        <thead><tr><th>Data</th><th>Status</th><th>Responsável</th><th>Resposta</th></tr></thead>
        <tbody>
          <?php foreach ($movs as $m): ?>
            <tr>
              <td><?php echo e(date('d/m/Y H:i', strtotime($m['created_at']))); ?></td>
              <td><?php echo e($m['status_nome']); ?></td>
              <td><?php echo e($m['admin_nome'] ?: '-'); ?></td>
              <td><?php echo nl2br(e($m['resposta'])); ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table></div>
    <?php endif; ?>
  </div>
<?php
else:
  // Lista com filtros + dados
  $params=[]; $where=build_filters($params);
  $sql = "SELECT t.*, rt.nome AS tipo_nome, s.nome AS setor_nome, ts.nome AS status_nome
          FROM tickets t
          JOIN request_types rt ON rt.id=t.tipo_id
          JOIN sectors s ON s.id=t.setor_id
          JOIN ticket_status ts ON ts.id=t.status_id
          $where
          ORDER BY t.opened_at DESC
          LIMIT 500";
  $list = $pdo->prepare($sql); $list->execute($params); $tickets = $list->fetchAll();

  $qs = http_build_query($_GET);
  $exportUrl = base_url('admin/relatorios.php?export=1' . ($qs ? '&' . $qs : ''));
?>
  <div class="card">
    <form method="get" class="grid cols-4">
      <div class="form-group"><label>Categoria</label>
        <select name="tipo_id"><option value="">Todas</option>
          <?php foreach ($tipos as $t): ?>
            <option value="<?php echo (int)$t['id']; ?>" <?php if (($_GET['tipo_id']??'')==$t['id']) echo 'selected'; ?>>
              <?php echo e($t['nome']); ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group"><label>Setor</label>
        <select name="setor_id"><option value="">Todos</option>
          <?php foreach ($setores as $s): ?>
            <option value="<?php echo (int)$s['id']; ?>" <?php if (($_GET['setor_id']??'')==$s['id']) echo 'selected'; ?>>
              <?php echo e($s['nome']); ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group"><label>Status</label>
        <select name="status_id"><option value="">Todos</option>
          <?php foreach ($statuses as $st): ?>
            <option value="<?php echo (int)$st['id']; ?>" <?php if (($_GET['status_id']??'')==$st['id']) echo 'selected'; ?>>
              <?php echo e($st['nome']); ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group"><label>Prioridade</label>
        <select name="prioridade">
          <option value="">Todas</option>
          <?php foreach (['Urgente','Média','Baixa'] as $p): ?>
            <option <?php if (($_GET['prioridade']??'')===$p) echo 'selected'; ?>><?php echo $p; ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group"><label>Local</label><input type="text" name="local" value="<?php echo e($_GET['local']??''); ?>"></div>
      <div class="form-group"><label>Curso</label><input type="text" name="curso" value="<?php echo e($_GET['curso']??''); ?>"></div>
      <div class="form-group"><label>Período inicial</label><input type="date" name="periodo_ini" value="<?php echo e($_GET['periodo_ini']??''); ?>"></div>
      <div class="form-group"><label>Período final</label><input type="date" name="periodo_fim" value="<?php echo e($_GET['periodo_fim']??''); ?>"></div>
      <div class="form-group" style="grid-column:1/-1;display:flex;gap:8px;flex-wrap:wrap;">
        <button class="btn primary" type="submit">Filtrar</button>
        <a class="btn" href="<?php echo $exportUrl; ?>">Exportar CSV</a>
      </div>
    </form>
  </div>

  <!-- Tabela (desktop) -->
  <div class="card">
    <div class="table-responsive">
      <table class="table tickets-table">
        <thead>
          <tr>
            <th class="col-prot">Protocolo</th>
            <th class="col-user">Usuário/Matrícula</th>
            <th class="col-cat">Categoria/Setor</th>
            <th class="col-local">Local</th>
            <th class="col-prio">Prioridade</th>
            <th class="col-stat">Status</th>
            <th class="col-open">Abertura</th>
            <th class="col-upd">Atualização</th>
            <th class="col-actions">Ações</th>
          </tr>
        </thead>
        <tbody>
        <?php if (!$tickets): ?>
          <tr><td colspan="9">Nenhum registro.</td></tr>
        <?php else: foreach ($tickets as $t):
          $p=strtolower($t['prioridade']); $cls=$p==='urgente'?'urgente':($p==='média'?'media':'baixa');
          $dtOpen = date('d/m/Y H:i', strtotime($t['opened_at']));
          $dtUpd  = date('d/m/Y H:i', strtotime($t['updated_at']));
        ?>
          <tr>
            <td class="col-prot"><?php echo e($t['protocolo']); ?></td>
            <td class="col-user"><?php echo e($t['users_nome']); ?><br><small><?php echo e($t['matricula']); ?></small></td>
            <td class="col-cat"><?php echo e($t['tipo_nome']); ?><br><small><?php echo e($t['setor_nome']); ?></small></td>
            <td class="col-local"><?php echo e($t['local_problema']); ?></td>
            <td class="col-prio"><span class="badge <?php echo $cls; ?>"><?php echo e($t['prioridade']); ?></span></td>
            <td class="col-stat"><?php echo e($t['status_nome']); ?></td>
            <td class="col-open"><?php echo e($dtOpen); ?></td>
            <td class="col-upd"><?php echo e($dtUpd); ?></td>
            <td class="col-actions">
              <div style="display:flex;gap:6px;flex-wrap:wrap;justify-content:flex-end;">
                <a class="btn" href="<?php echo base_url('admin/request.php?id='.(int)$t['id']); ?>">Ver</a>
                <form method="post" style="display:inline;">
                  <input type="hidden" name="csrf" value="<?php echo csrf_token(); ?>">
                  <input type="hidden" name="quick_update" value="1">
                  <input type="hidden" name="id" value="<?php echo (int)$t['id']; ?>">
                  <input type="hidden" name="status_id" value="2">
                  <button class="btn" type="submit">Em andamento</button>
                </form>
                <form method="post" style="display:inline;">
                  <input type="hidden" name="csrf" value="<?php echo csrf_token(); ?>">
                  <input type="hidden" name="quick_update" value="1">
                  <input type="hidden" name="id" value="<?php echo (int)$t['id']; ?>">
                  <input type="hidden" name="status_id" value="3">
                  <button class="btn success" type="submit">Concluir</button>
                </form>
              </div>
            </td>
          </tr>
        <?php endforeach; endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Grid (mobile) -->
  <div class="card">
    <div class="tickets-grid">
      <?php if (!$tickets): ?>
        <div class="ticket-card">Nenhum registro.</div>
      <?php else: foreach ($tickets as $t):
        $dtOpen = date('d/m/Y H:i', strtotime($t['opened_at']));
        $dtUpd  = date('d/m/Y H:i', strtotime($t['updated_at']));
        $cls = strtolower($t['prioridade'])==='urgente' ? 'urgente' : (strtolower($t['prioridade'])==='média' ? 'media' : 'baixa');
      ?>
        <div class="ticket-card">
          <div class="ticket-head">
            <div class="ticket-protocol"><?php echo e($t['protocolo']); ?></div>
            <div class="ticket-status"><?php echo e($t['status_nome']); ?></div>
          </div>
          <div class="ticket-user">
            <strong><?php echo e($t['users_nome']); ?></strong>
            <div><small><?php echo e($t['matricula']); ?></small></div>
          </div>

          <!-- Detalhes compactos escondidos se desejar (basta remover se não quiser) -->
          <div class="ticket-meta">
            <div class="meta-item"><strong>Categoria/Setor</strong><?php echo e($t['tipo_nome']); ?> / <?php echo e($t['setor_nome']); ?></div>
            <div class="meta-item"><strong>Local</strong><?php echo e($t['local_problema']); ?></div>
            <div class="meta-item"><strong>Prioridade</strong><span class="badge <?php echo $cls; ?>"><?php echo e($t['prioridade']); ?></span></div>
            <div class="meta-item"><strong>Abertura</strong><?php echo e($dtOpen); ?></div>
            <div class="meta-item"><strong>Atualização</strong><?php echo e($dtUpd); ?></div>
          </div>

          <div class="ticket-actions">
            <a class="btn" href="<?php echo base_url('admin/request.php?id='.(int)$t['id']); ?>">Ver</a>
            <form method="post">
              <input type="hidden" name="csrf" value="<?php echo csrf_token(); ?>">
              <input type="hidden" name="quick_update" value="1">
              <input type="hidden" name="id" value="<?php echo (int)$t['id']; ?>">
              <input type="hidden" name="status_id" value="2">
              <button class="btn" type="submit">Em andamento</button>
            </form>
            <form method="post">
              <input type="hidden" name="csrf" value="<?php echo csrf_token(); ?>">
              <input type="hidden" name="quick_update" value="1">
              <input type="hidden" name="id" value="<?php echo (int)$t['id']; ?>">
              <input type="hidden" name="status_id" value="3">
              <button class="btn success" type="submit">Concluir</button>
            </form>
          </div>
        </div>
      <?php endforeach; endif; ?>
    </div>
  </div>

<?php
endif;
require __DIR__ . '/footer.php';
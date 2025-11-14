<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../config/auth.php';
require_admin();

if (isset($_GET['export']) && $_GET['export']=='1') {
  $params = []; $where = [];
  if (!empty($_GET['tipo_id'])) { $where[]="t.tipo_id=:tipo_id"; $params[':tipo_id']=(int)$_GET['tipo_id']; }
  if (!empty($_GET['setor_id'])) { $where[]="t.setor_id=:setor_id"; $params[':setor_id']=(int)$_GET['setor_id']; }
  if (!empty($_GET['status_id'])) { $where[]="t.status_id=:status_id"; $params[':status_id']=(int)$_GET['status_id']; }
  if (!empty($_GET['prioridade'])) { $where[]="t.prioridade=:prioridade"; $params[':prioridade']=$_GET['prioridade']; }
  if (!empty($_GET['local'])) { $where[]="t.local_problema LIKE :local"; $params[':local']='%'.$_GET['local'].'%'; }
  if (!empty($_GET['curso'])) { $where[]="t.curso LIKE :curso"; $params[':curso']='%'.$_GET['curso'].'%'; }
  if (!empty($_GET['periodo_ini'])) { $where[]="DATE(t.opened_at)>=:ini"; $params[':ini']=$_GET['periodo_ini']; }
  if (!empty($_GET['periodo_fim'])) { $where[]="DATE(t.opened_at)<=:fim"; $params[':fim']=$_GET['periodo_fim']; }

  $whereSql = $where ? (' WHERE ' . implode(' AND ', $where)) : '';
  $sql = "SELECT t.protocolo, t.users_nome, t.matricula, t.cargo, t.curso, t.local_problema,
                 rt.nome AS categoria, s.nome AS setor, t.prioridade, ts.nome AS status,
                 t.opened_at, t.updated_at
          FROM tickets t
          JOIN request_types rt ON rt.id=t.tipo_id
          JOIN sectors s ON s.id=t.setor_id
          JOIN ticket_status ts ON ts.id=t.status_id
          $whereSql
          ORDER BY t.opened_at DESC";
  $st = $pdo->prepare($sql); $st->execute($params); $rows = $st->fetchAll();

  header('Content-Type: text/csv; charset=utf-8');
  header('Content-Disposition: attachment; filename=relatorio_chamados.csv');
  $out = fopen('php://output', 'w');
  fputcsv($out, ['Protocolo','Usuário','Matrícula','Cargo','Curso','Local','Categoria','Setor','Prioridade','Status','Abertura','Atualização'], ';');
  foreach ($rows as $r) {
    fputcsv($out, [
      $r['protocolo'],$r['users_nome'],$r['matricula'],$r['cargo'],$r['curso'],
      $r['local_problema'],$r['categoria'],$r['setor'],$r['prioridade'],$r['status'],
      date('d/m/Y H:i', strtotime($r['opened_at'])), date('d/m/Y H:i', strtotime($r['updated_at']))
    ], ';');
  }
  fclose($out); exit;
}

$tipos = $pdo->query("SELECT id,nome FROM request_types WHERE ativo=1 ORDER BY nome")->fetchAll();
$setores = $pdo->query("SELECT id,nome FROM sectors WHERE ativo=1 ORDER BY nome")->fetchAll();
$statuses = $pdo->query("SELECT id,nome FROM ticket_status WHERE ativo=1 ORDER BY id")->fetchAll();

$pageTitle = 'Relatórios';
require __DIR__ . '/header.php';
?>
<div class="card">
  <form method="get" class="grid cols-4">
    <input type="hidden" name="export" value="1">
    <div class="form-group"><label>Categoria</label>
      <select name="tipo_id"><option value="">Todas</option>
        <?php foreach ($tipos as $t): ?><option value="<?php echo (int)$t['id']; ?>"><?php echo e($t['nome']); ?></option><?php endforeach; ?>
      </select>
    </div>
    <div class="form-group"><label>Setor</label>
      <select name="setor_id"><option value="">Todos</option>
        <?php foreach ($setores as $s): ?><option value="<?php echo (int)$s['id']; ?>"><?php echo e($s['nome']); ?></option><?php endforeach; ?>
      </select>
    </div>
    <div class="form-group"><label>Status</label>
      <select name="status_id"><option value="">Todos</option>
        <?php foreach ($statuses as $st): ?><option value="<?php echo (int)$st['id']; ?>"><?php echo e($st['nome']); ?></option><?php endforeach; ?>
      </select>
    </div>
    <div class="form-group"><label>Prioridade</label>
      <select name="prioridade"><option value="">Todas</option><option>Urgente</option><option>Média</option><option>Baixa</option></select>
    </div>
    <div class="form-group"><label>Local</label><input type="text" name="local"></div>
    <div class="form-group"><label>Curso</label><input type="text" name="curso"></div>
    <div class="form-group"><label>Período inicial</label><input type="date" name="periodo_ini"></div>
    <div class="form-group"><label>Período final</label><input type="date" name="periodo_fim"></div>
    <div class="form-group" style="grid-column:1/-1;">
      <button class="btn primary" type="submit">Exportar CSV</button>
    </div>
  </form>
</div>
<?php require __DIR__ . '/footer.php'; ?>
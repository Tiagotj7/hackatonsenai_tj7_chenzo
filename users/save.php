<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../config/mailer.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  redirect('users/nova.php');
}
verify_csrf();

$nome = trim($_POST['users_nome'] ?? '');
$matricula = trim($_POST['matricula'] ?? '');
$cargo = trim($_POST['cargo'] ?? '');
$curso = trim($_POST['curso'] ?? '');
$local = trim($_POST['local_problema'] ?? '');
$descricao = trim($_POST['descricao'] ?? '');
$tipo_id = (int)($_POST['tipo_id'] ?? 0);
$prioridade = $_POST['prioridade'] ?? '';
$email = trim($_POST['email'] ?? '');

$erros = [];
foreach ([
  'Nome'=>$nome,'Matrícula'=>$matricula,'Cargo'=>$cargo,'Local'=>$local,
  'Descrição'=>$descricao,'Categoria'=>$tipo_id,'Prioridade'=>$prioridade
] as $campo=>$val) {
  if (empty($val)) $erros[] = "Campo obrigatório: $campo.";
}
if (!in_array($prioridade, ['Urgente','Média','Baixa'], true)) $erros[]='Prioridade inválida.';

$st = $pdo->prepare("SELECT setor_id FROM request_types WHERE id=:id AND ativo=1");
$st->execute([':id'=>$tipo_id]);
$tipoRow = $st->fetch();
if (!$tipoRow) $erros[]='Categoria inválida.';
$setor_id = $tipoRow['setor_id'] ?? null;

$image_path = null;
if (!empty($_FILES['imagem']['name'])) {
  $up = upload_imagem($_FILES['imagem']);
  if ($up === false) $erros[]='Falha no upload da imagem.'; else $image_path = $up;
}

if ($erros) {
  foreach ($erros as $err) flash('error', $err);
  redirect('users/nova.php');
}

$protocolo = gerar_protocolo($pdo);
$pdo->beginTransaction();
try {
  $sql = "INSERT INTO tickets (protocolo, users_nome, matricula, cargo, curso, local_problema, descricao, tipo_id, setor_id, prioridade, email, status_id, image_path)
          VALUES (:p,:n,:m,:c,:curso,:l,:d,:tipo,:setor,:pri,:e,1,:img)";
  $ins = $pdo->prepare($sql);
  $ins->execute([
    ':p'=>$protocolo, ':n'=>$nome, ':m'=>$matricula, ':c'=>$cargo, ':curso'=>$curso?:null,
    ':l'=>$local, ':d'=>$descricao, ':tipo'=>$tipo_id, ':setor'=>$setor_id, ':pri'=>$prioridade,
    ':e'=>$email?:null, ':img'=>$image_path
  ]);
  $ticket_id = (int)$pdo->lastInsertId();

  $pdo->prepare("INSERT INTO ticket_movements (ticket_id, user_id, status_id, resposta)
                 VALUES (:t,NULL,1,'Solicitação registrada.')")->execute([':t'=>$ticket_id]);

  $pdo->commit();

  if (EMAIL_ENABLED) {
    $s = $pdo->prepare("SELECT email FROM sectors WHERE id=:id AND ativo=1");
    $s->execute([':id'=>$setor_id]);
    if ($emailSetor = $s->fetchColumn()) {
      $body = "<p>Nova solicitação: <strong>$protocolo</strong></p>
               <p>Prioridade: $prioridade</p>
               <p>Local: $local</p>
               <p>Descrição:<br>".nl2br(e($descricao))."</p>";
      @send_email($emailSetor, "[SENAI] Novo Chamado $protocolo", $body);
    }
  }

  flash('success', "Solicitação criada! Protocolo: $protocolo");
  redirect('users/minhas.php?matricula=' . urlencode($matricula));
} catch (Exception $e) {
  $pdo->rollBack();
  flash('error', 'Erro ao salvar: ' . $e->getMessage());
  redirect('users/nova.php');
}
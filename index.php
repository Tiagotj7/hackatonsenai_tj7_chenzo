<?php 

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="assets/css/style.css" />
    <title>SENAI Service Manager</title>
  </head>
  <body>
    <main>
      <section class="presentation">
        <h1>SENAI Service Manager</h1>
        <p>>Bem-vindo ao Gerenciador de Serviços SENAI.</p>
        <section class="description">
          <p>
            Este sistema foi desenvolvido para facilitar a gestão de serviços
            oferecidos pelo SENAI. Aqui, administradores podem gerenciar
            serviços, enquanto usuários podem solicitar e acompanhar seus
            serviços de forma eficiente.
          </p>
        </section>
      </section>
      <section class="selection">
        <p>Selecione uma opção abaixo para prosseguir.</p>
        <a id="selection_admin" href="/admin/login.php">Administrador</a>
        <a id="selection_users" href="/users/interface.php">Usuário</a>
      </section>
    </main>
  </body>
</html>

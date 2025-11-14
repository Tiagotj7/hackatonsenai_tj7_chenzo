# SENAI Chamados (PHP + MySQL)

Sistema web para gerenciamento de solicitações internas (TI/Manutenção).

## Requisitos
- PHP 8+
- MySQL 5.7+ / 8.0
- Apache (.htaccess habilitado)
- InfinityFree (opcional, para deploy)

## Instalação local
1. Crie o banco via phpMyAdmin importando `db/database.sql`.
2. Copie `.env` (exemplo no README) e configure `BASE_URL` e MySQL.
3. Dê permissão de escrita à pasta `uploads/`.
4. Acesse `http://localhost/sua-pasta/index.php`.
5. Admin: `admin/login.php` → se não houver usuário, use "Criar admin padrão".

## Deploy InfinityFree
1. Crie o banco e anote host/usuário/senha.
2. Suba os arquivos para `htdocs/`.
3. Edite `.env` com `BASE_URL` e credenciais de banco.
4. Importe `db/database.sql` no phpMyAdmin do InfinityFree.
5. Acesse o domínio.

## Recursos
- Solicitante (abrir, listar por matrícula, detalhes, upload).
- Admin (login, dashboard, filtros, histórico, atualização de status).
- CSV export, dark mode, CSRF, prepared statements.

## Segurança
- Credenciais via `.env`.
- `.htaccess` bloqueando `config/`, `db/` e `.env`.
- CSRF token em POST.

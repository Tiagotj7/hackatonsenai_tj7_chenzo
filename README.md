# Senai Service Manager

Sistema web para gerenciamento de solicitações internas (TI/Manutenção) do SENAI.

## Requisitos
- PHP 8+ (InfinityFree OK)
- MySQL (InfinityFree) + phpMyAdmin
- Apache com .htaccess habilitado

## Instalação no InfinityFree
1. Crie um banco em MySQL Databases e anote:
   - DB Host (ex.: sql201.epizy.com)
   - DB Name (ex.: epiz_12345678_senai)
   - DB User (ex.: epiz_12345678)
   - DB Pass
2. Acesse phpMyAdmin e importe `db/database.sql`.
3. Envie todos os arquivos para `htdocs/` via FTP.
4. Crie e edite o arquivo `.env` na raiz (htdocs) com suas credenciais:
   - APP_NAME="Senai Service Manager"
   - BASE_URL="https://seu-dominio.epizy.com"
   - DB_HOST/DB_NAME/DB_USER/DB_PASS
5. Garanta que a pasta `uploads/` existe em `htdocs/` e tem permissão de escrita.
6. Acesse:
   - Público: https://seu-dominio.epizy.com/index.php
   - Admin: https://seu-dominio.epizy.com/admin/login.php
   - Na primeira vez, clique em "Criar admin padrão" (admin/admin123) e depois altere a senha.

## Recursos
- Abertura de solicitação (upload de imagem, prioridade, categoria).
- Acompanhamento por matrícula (status, histórico, datas).
- Painel admin com filtros, atualização de status, histórico de movimentações.
- Dashboard com KPIs e distribuição por prioridade.
- Exportação CSV.
- Dark mode.
- Segurança: .env, .htaccess, CSRF, prepared statements.

## Observações InfinityFree
- E-mails: geralmente `mail()` não funciona no plano free. Deixe `EMAIL_ENABLED=false` no `.env`.
- O host do MySQL não é `localhost`. Use o host fornecido (sqlXXX.epizy.com).
- Se o upload falhar por restrição do host, tente reduzir `MAX_UPLOAD_MB` no `.env`.
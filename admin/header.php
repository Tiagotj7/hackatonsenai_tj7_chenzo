<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../config/auth.php';

$isLogged = !empty($_SESSION['admin_user']);
$adm = $isLogged ? $_SESSION['admin_user'] : null;
$current = basename($_SERVER['PHP_SELF']);
function active($file) {
  global $current;
  return $current === $file ? 'active' : '';
}
?>
<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <title><?php echo isset($pageTitle) ? e($pageTitle).' - ' : ''; ?><?php echo e(app_name()); ?></title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="<?php echo base_url('assets/css/style.css'); ?>">
  <style>
    /* ===== Header/Admin Nav responsivo + Sidebar ===== */
    .navbar { position: sticky; top: 0; z-index: 60; }
    .admin-brand { display:flex; align-items:center; gap:10px; min-width:0; }
    .admin-brand h1 { font-size: 18px; margin:0; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    .admin-actions { display:flex; align-items:center; gap:10px; }
    .admin-nav-links { display:flex; align-items:center; gap:8px; }
    .admin-nav-links a { white-space:nowrap; }
    .admin-nav-links .active { border-color: var(--primary); box-shadow: 0 0 0 2px rgba(37,99,235,.12); }

    /* Hambúrguer (aparece no mobile) */
    .admin-hamburger {
      appearance:none; -webkit-appearance:none;
      background: transparent; border: 1px solid var(--border);
      width: 38px; height: 38px; border-radius: 8px;
      display: none; align-items: center; justify-content: center;
      cursor: pointer;
    }
    .admin-hamburger span {
      display:block; width:18px; height:2px; background: var(--text);
      border-radius: 2px; position:relative;
    }
    .admin-hamburger span::before, .admin-hamburger span::after {
      content:''; position:absolute; left:0; width:18px; height:2px; background: var(--text); border-radius:2px;
    }
    .admin-hamburger span::before { top:-6px; }
    .admin-hamburger span::after  { top: 6px; }

    /* Sidebar (drawer) */
    .admin-backdrop {
      position: fixed; inset: 0;
      background: rgba(0,0,0,.35);
      opacity: 0; visibility: hidden;
      transition: .2s ease; z-index: 98;
    }
    .admin-backdrop.open { opacity: 1; visibility: visible; }

    .admin-sidebar {
      position: fixed; top:0; left:0; height:100%; width: 290px; max-width: 86vw;
      background: var(--card); border-right:1px solid var(--border);
      transform: translateX(-100%); transition: transform .22s ease;
      z-index: 99; display: flex; flex-direction: column;
      box-shadow: var(--shadow-lg);
    }
    .admin-sidebar.open { transform: translateX(0); }

    .admin-sidebar__header {
      display:flex; align-items:center; gap:10px; padding: 14px 14px; border-bottom: 1px solid var(--border);
    }
    .admin-avatar {
      width:38px; height:38px; border-radius: 50%; display:grid; place-items:center;
      background: rgba(37,99,235,.15); color: var(--primary); font-weight: 800;
    }
    .admin-sidebar__nav { padding: 10px; display:flex; flex-direction: column; gap:8px; overflow:auto; }
    .admin-link {
      display:flex; align-items:center; gap:8px; padding: 10px 12px;
      border:1px solid var(--border); border-radius: 10px;
      color: var(--text); background: var(--card); text-decoration: none;
    }
    .admin-link:hover { background: var(--bg-soft); }
    .admin-link.active { border-color: var(--primary); background: rgba(37,99,235,.08); }
    .admin-sidebar__footer { margin-top:auto; padding: 12px; border-top: 1px dashed var(--border); }
    .btn.full { width:100%; }

    /* Responsividade */
    @media (max-width: 980px) {
      .admin-nav-links { display: none; }
      .admin-hamburger { display: inline-flex; }
      .admin-actions { gap:8px; }
      .navbar .container { gap:10px; }
    }
    @media (max-width: 420px) {
      .admin-brand h1 { font-size: 16px; }
      .admin-actions label { font-size: 12px; }
    }
  </style>
</head>
<body>
  <header class="navbar">
    <div class="container" style="display:flex; align-items:center; justify-content:space-between; gap:12px;">
      <div class="admin-brand" style="min-width:0;">
        <button class="admin-hamburger" id="menuToggle" aria-label="Abrir menu" aria-controls="adminSidebar" aria-expanded="false">
          <span></span>
        </button>
        <h1><?php echo e(app_name()); ?></h1>
      </div>

<!-- Toggle de tema (light/dark) -->
<label class="theme-switch" title="Alternar tema">
  <input id="darkToggle" type="checkbox" aria-label="Alternar tema claro/escuro">
  <span class="track">
    <span class="icon sun" aria-hidden="true">☀️</span>
    <span class="icon moon" aria-hidden="true">🌙</span>
    <span class="thumb"></span>
  </span>
</label>

        <nav class="admin-nav-links">
          <?php if ($isLogged): ?>
            <span class="text-muted" style="white-space:nowrap;">Olá, <?php echo e($adm['nome']); ?></span>
            <a class="btn <?php echo active('dashboard.php'); ?>" href="<?php echo base_url('admin/dashboard.php'); ?>">Dashboard</a>
            <a class="btn <?php echo active('request.php'); ?>" href="<?php echo base_url('admin/request.php'); ?>">Tickets</a>
            <a class="btn <?php echo active('relatorios.php'); ?>" href="<?php echo base_url('admin/relatorios.php'); ?>">Relatórios</a>
            <a class="btn danger" href="<?php echo base_url('admin/logout.php'); ?>">Sair</a>
          <?php else: ?>
            <a class="btn" href="<?php echo base_url('admin/login.php'); ?>">Login</a>
          <?php endif; ?>
        </nav>
      </div>
    </div>
  </header>

  <!-- Sidebar + Backdrop (só fica visível no mobile ao abrir) -->
  <?php if ($isLogged): ?>
    <div class="admin-backdrop" id="sidebarBackdrop" aria-hidden="true"></div>
    <aside class="admin-sidebar" id="adminSidebar" role="dialog" aria-modal="true" aria-label="Menu">
      <div class="admin-sidebar__header">
        <div class="admin-avatar"><?php echo strtoupper(mb_substr($adm['nome'] ?? 'A', 0, 1, 'UTF-8')); ?></div>
        <div style="min-width:0;">
          <strong style="display:block; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;"><?php echo e($adm['nome']); ?></strong>
          <span class="text-muted" style="font-size:12px;"><?php echo e($adm['cargo'] ?? 'Administrador'); ?></span>
        </div>
      </div>
      <nav class="admin-sidebar__nav">
        <a class="admin-link <?php echo active('dashboard.php'); ?>" href="<?php echo base_url('admin/dashboard.php'); ?>">🏠 Dashboard</a>
        <a class="admin-link <?php echo active('request.php'); ?>" href="<?php echo base_url('admin/request.php'); ?>">🎫 Tickets</a>
        <a class="admin-link <?php echo active('relatorios.php'); ?>" href="<?php echo base_url('admin/relatorios.php'); ?>">📄 Relatórios</a>
      </nav>
      <div class="admin-sidebar__footer">
        <a class="btn danger full" href="<?php echo base_url('admin/logout.php'); ?>">Sair</a>
      </div>
    </aside>
  <?php endif; ?>

  <div class="container">
    <?php foreach (get_flash() as $f): ?>
      <div class="alert <?php echo e($f['type']); ?>"><?php echo e($f['message']); ?></div>
    <?php endforeach; ?>

  <script>
    // Toggle da sidebar (mobile)
    document.addEventListener('DOMContentLoaded', function(){
      var btn = document.getElementById('menuToggle');
      var sidebar = document.getElementById('adminSidebar');
      var backdrop = document.getElementById('sidebarBackdrop');
      if (!btn || !sidebar || !backdrop) return;

      function openMenu(){
        sidebar.classList.add('open');
        backdrop.classList.add('open');
        btn.setAttribute('aria-expanded', 'true');
        document.documentElement.style.overflow = 'hidden';
      }
      function closeMenu(){
        sidebar.classList.remove('open');
        backdrop.classList.remove('open');
        btn.setAttribute('aria-expanded', 'false');
        document.documentElement.style.overflow = '';
      }

      btn.addEventListener('click', function(){
        if (sidebar.classList.contains('open')) closeMenu(); else openMenu();
      });
      backdrop.addEventListener('click', closeMenu);
      document.addEventListener('keydown', function(e){ if (e.key === 'Escape') closeMenu(); });

      // Fecha ao clicar em um link da sidebar
      sidebar.querySelectorAll('a').forEach(function(a){
        a.addEventListener('click', closeMenu);
      });
    });
  </script>
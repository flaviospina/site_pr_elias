<?php use App\Core\Csrf; ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="robots" content="noindex">
<title><?= e($pageTitle ?? 'Painel') ?> — Admin</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= asset_url('assets/css/style.css') ?>">
</head>
<body class="admin-body">
<div class="admin-shell">
  <aside class="admin-sidebar" data-admin-sidebar>
    <div class="admin-brand">
      <strong>Painel do Site</strong>
      <small><?= e($settings['site_name'] ?? '') ?></small>
    </div>
    <nav class="admin-nav">
      <a href="<?= base_url('admin') ?>">📊 Painel</a>
      <span class="admin-nav-label">Loja</span>
      <a href="<?= base_url('admin/livros') ?>">📚 Livros</a>
      <a href="<?= base_url('admin/pedidos') ?>">🧾 Pedidos</a>
      <a href="<?= base_url('admin/pagamentos') ?>">💳 Pagamentos</a>
      <a href="<?= base_url('admin/depoimentos') ?>">⭐ Depoimentos</a>
      <span class="admin-nav-label">Conteúdo</span>
      <a href="<?= base_url('admin/sermoes') ?>">🎙 Sermões</a>
      <a href="<?= base_url('admin/devocionais') ?>">📖 Devocionais</a>
      <a href="<?= base_url('admin/agenda') ?>">📅 Agenda</a>
      <a href="<?= base_url('admin/paginas') ?>">📄 Páginas</a>
      <span class="admin-nav-label">Comunicação</span>
      <a href="<?= base_url('admin/mensagens') ?>">✉️ Mensagens</a>
      <a href="<?= base_url('admin/inscritos') ?>">👥 Inscritos</a>
      <span class="admin-nav-label">Sistema</span>
      <a href="<?= base_url('admin/configuracoes') ?>">⚙️ Configurações</a>
      <a href="<?= base_url('admin/usuarios') ?>">🔐 Usuários</a>
    </nav>
    <div class="admin-sidebar-footer">
      <a href="<?= base_url() ?>" target="_blank" rel="noopener">🌐 Ver o site</a>
      <form method="post" action="<?= base_url('admin/logout') ?>">
        <?= Csrf::field() ?>
        <button type="submit" class="link-button">Sair</button>
      </form>
    </div>
  </aside>
  <div class="admin-main">
    <header class="admin-topbar">
      <button class="nav-toggle" aria-label="Menu" data-admin-toggle><span></span><span></span><span></span></button>
      <h1><?= e($pageTitle ?? '') ?></h1>
      <span class="admin-user">Olá, <?= e($_SESSION['admin_name'] ?? '') ?></span>
    </header>
    <?php if ($msg = flash('success')): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>
    <?php if ($msg = flash('error')): ?><div class="alert alert-error"><?= e($msg) ?></div><?php endif; ?>
    <div class="admin-content">
      <?= $content ?>
    </div>
  </div>
</div>
<script>window.CSRF_TOKEN = <?= json_encode(Csrf::token()) ?>;</script>
<script src="<?= asset_url('assets/js/main.js') ?>" defer></script>
</body>
</html>

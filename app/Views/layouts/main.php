<?php use App\Core\Cart; use App\Core\Csrf; ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e(isset($pageTitle) && $pageTitle ? $pageTitle . ' — ' . ($settings['site_name'] ?? '') : ($settings['site_name'] ?? '') . ' | ' . ($settings['site_tagline'] ?? '')) ?></title>
<meta name="description" content="<?= e($metaDescription ?? 'Sermões, devocionais e livros do Pr. Elias José da Silva. Conteúdo bíblico para fortalecer a sua fé.') ?>">
<link rel="icon" href="<?= e($settings['site_logo'] ?? '') ?>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,600;0,700;1,600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
</head>
<body>

<?php if (!empty($settings['announcement_bar'])): ?>
<div class="announcement"><?= e($settings['announcement_bar']) ?></div>
<?php endif; ?>

<header class="site-header">
  <div class="container header-inner">
    <a class="brand" href="<?= base_url() ?>">
      <?php if (!empty($settings['site_logo'])): ?>
        <img src="<?= e($settings['site_logo']) ?>" alt="<?= e($settings['site_name'] ?? '') ?>" class="brand-logo">
      <?php endif; ?>
      <span class="brand-text">
        <strong><?= e($settings['site_name'] ?? 'Pr. Elias José da Silva') ?></strong>
        <small><?= e($settings['site_tagline'] ?? '') ?></small>
      </span>
    </a>
    <button class="nav-toggle" aria-label="Abrir menu" aria-expanded="false" data-nav-toggle>
      <span></span><span></span><span></span>
    </button>
    <nav class="site-nav" data-nav>
      <a href="<?= base_url() ?>">Início</a>
      <a href="<?= base_url('sobre-o-pr') ?>">Sobre o Pr.</a>
      <a href="<?= base_url('livros') ?>">Livros</a>
      <a href="<?= base_url('sermoes') ?>">Sermões</a>
      <a href="<?= base_url('devocionais') ?>">Devocionais</a>
      <a href="<?= base_url('agenda') ?>">Agenda</a>
      <a href="<?= base_url('contato') ?>">Contato</a>
      <a href="<?= base_url('carrinho') ?>" class="nav-cart" aria-label="Carrinho">
        🛒<?php $cartCount = Cart::count(); if ($cartCount): ?><span class="cart-badge"><?= $cartCount ?></span><?php endif; ?>
      </a>
    </nav>
  </div>
</header>

<?php if ($msg = flash('success')): ?><div class="container"><div class="alert alert-success"><?= e($msg) ?></div></div><?php endif; ?>
<?php if ($msg = flash('error')): ?><div class="container"><div class="alert alert-error"><?= e($msg) ?></div></div><?php endif; ?>

<main>
<?= $content ?>
</main>

<footer class="site-footer">
  <div class="container footer-grid">
    <div class="footer-col footer-about">
      <h3><?= e($settings['site_name'] ?? '') ?></h3>
      <p>Pastor, escritor e pregador. Dedicado à edificação do povo de Deus por meio da Palavra e do Espírito Santo.</p>
      <?php if (!empty($settings['footer_verse'])): ?>
      <p class="footer-verse"><?= e($settings['footer_verse']) ?></p>
      <?php endif; ?>
    </div>
    <div class="footer-col">
      <h4>Loja</h4>
      <a href="<?= base_url('livros') ?>">Todos os livros</a>
      <a href="<?= base_url('carrinho') ?>">Carrinho</a>
      <a href="<?= base_url('finalizar-compra') ?>">Finalizar compra</a>
    </div>
    <div class="footer-col">
      <h4>Ministério</h4>
      <a href="<?= base_url('sermoes') ?>">Sermões</a>
      <a href="<?= base_url('devocionais') ?>">Devocionais</a>
      <a href="<?= base_url('agenda') ?>">Agenda</a>
      <a href="<?= base_url('sobre-o-pr') ?>">Sobre o pastor</a>
    </div>
    <div class="footer-col">
      <h4>Contato</h4>
      <?php if (!empty($settings['whatsapp_display'])): ?>
      <a href="https://wa.me/<?= e($settings['whatsapp'] ?? '') ?>" target="_blank" rel="noopener">WhatsApp <?= e($settings['whatsapp_display']) ?></a>
      <?php endif; ?>
      <?php if (!empty($settings['contact_email'])): ?>
      <a href="mailto:<?= e($settings['contact_email']) ?>"><?= e($settings['contact_email']) ?></a>
      <?php endif; ?>
      <div class="footer-seals">
        <span class="seal" title="Conexão criptografada">🔒 Site seguro — SSL</span>
        <span class="seal" title="Garantia de satisfação">✔ Garantia de <?= e($settings['guarantee_days'] ?? '7') ?> dias</span>
        <span class="seal" title="Lei Geral de Proteção de Dados">🛡 LGPD</span>
      </div>
    </div>
  </div>
  <div class="container footer-bottom">
    <p>© <?= date('Y') ?> <?= e($settings['site_name'] ?? '') ?> — Todos os direitos reservados</p>
    <p>
      <a href="<?= base_url('politica-de-privacidade') ?>">Política de Privacidade</a> ·
      <a href="<?= base_url('termos-e-condicoes') ?>">Termos e Condições</a> ·
      <a href="#" data-cookie-prefs>Preferências de cookies</a>
    </p>
  </div>
</footer>

<?php if (!empty($settings['whatsapp'])): ?>
<a class="whatsapp-fab" href="https://wa.me/<?= e($settings['whatsapp']) ?>" target="_blank" rel="noopener" aria-label="Falar no WhatsApp">
  <svg viewBox="0 0 32 32" width="28" height="28" fill="currentColor" aria-hidden="true"><path d="M16 3C9.4 3 4 8.4 4 15c0 2.1.6 4.2 1.7 6L4 29l8.2-1.6c1.2.6 2.5.9 3.8.9 6.6 0 12-5.4 12-12S22.6 3 16 3zm0 21.8c-1.2 0-2.4-.3-3.5-.9l-.5-.3-4.9 1 1-4.7-.3-.5c-1-1.6-1.5-3.4-1.5-5.4 0-5.4 4.4-9.8 9.8-9.8s9.8 4.4 9.8 9.8-4.5 9.8-9.9 9.8zm5.4-7.3c-.3-.1-1.8-.9-2-1-.3-.1-.5-.1-.7.1-.2.3-.8 1-.9 1.2-.2.2-.3.2-.6.1-.3-.1-1.3-.5-2.4-1.5-.9-.8-1.5-1.8-1.7-2.1-.2-.3 0-.5.1-.6l.5-.6c.1-.2.2-.3.3-.5.1-.2 0-.4 0-.5l-.9-2.2c-.2-.6-.5-.5-.7-.5h-.6c-.2 0-.5.1-.8.4-.3.3-1 1-1 2.5s1.1 2.9 1.2 3.1c.1.2 2.1 3.2 5.1 4.5.7.3 1.3.5 1.7.6.7.2 1.4.2 1.9.1.6-.1 1.8-.7 2-1.4.3-.7.3-1.3.2-1.4-.1-.2-.3-.2-.6-.4z"/></svg>
</a>
<?php endif; ?>

<?php if (($settings['cookie_banner_enabled'] ?? '1') === '1'): ?>
<div class="cookie-banner" data-cookie-banner hidden>
  <div class="cookie-text">
    <strong>🍪 Este site usa cookies</strong>
    <p>Usamos cookies essenciais para o funcionamento do site (como o carrinho de compras) e, com o seu consentimento, cookies de estatísticas — conforme a LGPD. <a href="<?= base_url('politica-de-privacidade') ?>">Saiba mais</a>.</p>
  </div>
  <div class="cookie-actions">
    <button class="btn btn-outline btn-sm" data-cookie-reject>Somente essenciais</button>
    <button class="btn btn-primary btn-sm" data-cookie-accept>Aceitar todos</button>
  </div>
</div>
<?php endif; ?>

<script>window.CSRF_TOKEN = <?= json_encode(Csrf::token()) ?>;</script>
<script src="<?= base_url('assets/js/main.js') ?>" defer></script>
</body>
</html>

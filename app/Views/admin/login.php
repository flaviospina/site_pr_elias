<?php use App\Core\Csrf; ?>
<div class="login-wrap">
  <form class="login-card" method="post" action="<?= base_url('admin/login') ?>">
    <?= Csrf::field() ?>
    <h1>Painel do Site</h1>
    <p class="login-sub"><?= e($settings['site_name'] ?? '') ?></p>
    <?php if ($msg = flash('error')): ?><div class="alert alert-error"><?= e($msg) ?></div><?php endif; ?>
    <label>E-mail<input type="email" name="email" required autofocus autocomplete="username"></label>
    <label>Senha<input type="password" name="password" required autocomplete="current-password"></label>
    <button class="btn btn-primary btn-lg btn-block" type="submit">Entrar</button>
    <p class="login-note">🔒 Acesso restrito e protegido contra tentativas repetidas.</p>
  </form>
</div>

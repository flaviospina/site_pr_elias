<?php use App\Core\Csrf; ?>
<div class="panel">
  <div class="panel-head"><h2>Mensagens de contato</h2></div>
  <?php if (!$messages): ?>
    <p class="empty-state">Nenhuma mensagem recebida.</p>
  <?php else: ?>
  <div class="message-list">
    <?php foreach ($messages as $m): ?>
    <article class="message-item <?= $m['is_read'] ? '' : 'unread' ?>">
      <header>
        <strong><?= e($m['name']) ?></strong>
        <?= $m['subject'] ? '· ' . e($m['subject']) : '' ?>
        <span class="message-date"><?= date_br($m['created_at'], true) ?></span>
      </header>
      <p class="message-contact">
        <?= $m['email'] ? '✉ ' . e($m['email']) . ' ' : '' ?>
        <?= $m['phone'] ? '📱 ' . e($m['phone']) : '' ?>
      </p>
      <p><?= nl2br(e($m['message'])) ?></p>
      <footer>
        <?php if (!$m['is_read']): ?>
        <form method="post" action="<?= base_url('admin/mensagens/' . (int) $m['id'] . '/lida') ?>">
          <?= Csrf::field() ?><button class="btn btn-outline btn-sm" type="submit">Marcar como lida</button>
        </form>
        <?php endif; ?>
        <form method="post" action="<?= base_url('admin/mensagens/' . (int) $m['id'] . '/excluir') ?>" onsubmit="return confirm('Excluir esta mensagem?')">
          <?= Csrf::field() ?><button class="btn btn-danger btn-sm" type="submit">Excluir</button>
        </form>
      </footer>
    </article>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</div>

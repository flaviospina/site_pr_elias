<?php render_banner('agenda', [
    'title'    => 'Agenda',
    'subtitle' => 'Acompanhe os próximos compromissos, cultos e conferências do Pr. Elias.',
]); ?>

<section class="section">
  <div class="container">
    <?php if (!$upcoming): ?>
      <p class="empty-state">Nenhum evento agendado no momento. Volte em breve ou fale conosco pelo WhatsApp para convites.</p>
    <?php else: ?>
    <div class="event-list">
      <?php foreach ($upcoming as $event): ?>
      <article class="event-row">
        <div class="event-date">
          <strong><?= (int) date('d', strtotime($event['event_date'])) ?></strong>
          <span><?= month_br($event['event_date']) ?> <?= date('Y', strtotime($event['event_date'])) ?></span>
        </div>
        <div class="event-info">
          <h3><?= e($event['title']) ?></h3>
          <?php if ($event['description']): ?><p><?= e($event['description']) ?></p><?php endif; ?>
          <p class="event-meta">
            <?= e(trim(($event['location'] ? $event['location'] . ' — ' : '') . ($event['city'] ?? ''))) ?>
            <?= $event['event_time'] ? ' · ' . e($event['event_time']) : '' ?>
          </p>
          <?php if ($event['link']): ?><a class="card-link" href="<?= e($event['link']) ?>" target="_blank" rel="noopener">Mais informações →</a><?php endif; ?>
        </div>
      </article>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <div class="article-cta" style="margin-top:48px">
      <h3>Convites para pregações e eventos</h3>
      <p>Entre em contato pelo WhatsApp <?= e($settings['whatsapp_display'] ?? '') ?> para convidar o Pr. Elias para o seu evento.</p>
      <a class="btn btn-gold" href="https://wa.me/<?= e($settings['whatsapp'] ?? '') ?>" target="_blank" rel="noopener">Falar no WhatsApp →</a>
    </div>
  </div>
</section>

<?php use App\Core\Csrf; ?>
<?php render_banner('devocionais', [
    'title'    => 'Devocionais',
    'subtitle' => 'Reflexões bíblicas diárias para fortalecer a sua caminhada com Deus.',
]); ?>

<section class="section">
  <div class="container">
    <form class="search-bar" method="get" action="<?= base_url('devocionais') ?>">
      <input type="search" name="busca" value="<?= e($search) ?>" placeholder="Buscar devocional por título ou texto…">
      <button class="btn btn-primary" type="submit">Buscar</button>
    </form>

    <?php if (!$devotionals): ?>
      <p class="empty-state">Nenhum devocional encontrado<?= $search ? ' para “' . e($search) . '”' : '' ?>.</p>
    <?php else: ?>
    <div class="grid grid-3">
      <?php foreach ($devotionals as $dev): ?>
      <article class="content-card">
        <p class="content-card-meta"><?= e($dev['bible_reference'] ?: date_br($dev['published_at'])) ?></p>
        <h3><a href="<?= base_url('devocionais/' . $dev['slug']) ?>"><?= e($dev['title']) ?></a></h3>
        <p><?= e($dev['excerpt']) ?></p>
        <a class="card-link" href="<?= base_url('devocionais/' . $dev['slug']) ?>">Ler devocional →</a>
      </article>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <?php if ($totalPages > 1): ?>
    <nav class="pagination">
      <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <a class="<?= $i === $page ? 'active' : '' ?>" href="<?= base_url('devocionais?pagina=' . $i . ($search ? '&busca=' . urlencode($search) : '')) ?>"><?= $i ?></a>
      <?php endfor; ?>
    </nav>
    <?php endif; ?>

    <form class="newsletter boxed" method="post" action="<?= base_url('devocionais/inscrever') ?>">
      <?= Csrf::field() ?>
      <input type="hidden" name="_back" value="devocionais">
      <input type="text" name="website" class="hp-field" tabindex="-1" autocomplete="off" aria-hidden="true">
      <h3>Receba os devocionais por e-mail</h3>
      <div class="newsletter-fields">
        <input type="text" name="name" placeholder="Seu nome">
        <input type="email" name="email" placeholder="Seu melhor e-mail" required>
        <button class="btn btn-gold" type="submit">Quero receber</button>
      </div>
      <small>Ao se inscrever, você concorda com a nossa <a href="<?= base_url('politica-de-privacidade') ?>">Política de Privacidade</a> (LGPD).</small>
    </form>
  </div>
</section>

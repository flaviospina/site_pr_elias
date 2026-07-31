<?php render_banner('sermoes', [
    'title'    => 'Sermões',
    'subtitle' => 'Pregações expositivas, fiéis às Escrituras, para edificação da igreja.',
]); ?>

<section class="section">
  <div class="container">
    <form class="search-bar" method="get" action="<?= base_url('sermoes') ?>">
      <input type="search" name="busca" value="<?= e($search) ?>" placeholder="Buscar sermão por título ou texto…">
      <button class="btn btn-primary" type="submit">Buscar</button>
    </form>

    <?php if (!$sermons): ?>
      <p class="empty-state">Nenhum sermão encontrado<?= $search ? ' para “' . e($search) . '”' : '' ?>.</p>
    <?php else: ?>
    <div class="grid grid-3">
      <?php foreach ($sermons as $sermon): ?>
      <article class="content-card">
        <p class="content-card-meta"><?= e($sermon['bible_reference'] ?: date_br($sermon['published_at'])) ?></p>
        <h3><a href="<?= base_url('sermoes/' . $sermon['slug']) ?>"><?= e($sermon['title']) ?></a></h3>
        <p><?= e($sermon['excerpt']) ?></p>
        <a class="card-link" href="<?= base_url('sermoes/' . $sermon['slug']) ?>">Ler sermão →</a>
      </article>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <?php if ($totalPages > 1): ?>
    <nav class="pagination">
      <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <a class="<?= $i === $page ? 'active' : '' ?>" href="<?= base_url('sermoes?pagina=' . $i . ($search ? '&busca=' . urlencode($search) : '')) ?>"><?= $i ?></a>
      <?php endfor; ?>
    </nav>
    <?php endif; ?>
  </div>
</section>

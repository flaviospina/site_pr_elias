<article class="section">
  <div class="container article-page">
    <nav class="breadcrumb"><a href="<?= base_url() ?>">Início</a> / <a href="<?= base_url('devocionais') ?>">Devocionais</a></nav>
    <header class="article-head">
      <?php if ($devotional['bible_reference']): ?><p class="article-ref"><?= e($devotional['bible_reference']) ?></p><?php endif; ?>
      <h1><?= e($devotional['title']) ?></h1>
      <p class="article-meta">Pr. Elias José da Silva · <?= date_br($devotional['published_at']) ?></p>
    </header>

    <?php if ($devotional['image']): ?>
    <img class="article-image" src="<?= e($devotional['image']) ?>" alt="<?= e($devotional['title']) ?>">
    <?php endif; ?>

    <div class="article-body">
      <?= $devotional['content'] /* HTML sanitizado no admin */ ?>
    </div>

    <nav class="article-nav">
      <?php if ($prev): ?><a href="<?= base_url('devocionais/' . $prev['slug']) ?>">← <?= e($prev['title']) ?></a><?php else: ?><span></span><?php endif; ?>
      <?php if ($next): ?><a href="<?= base_url('devocionais/' . $next['slug']) ?>"><?= e($next['title']) ?> →</a><?php endif; ?>
    </nav>

    <aside class="article-cta">
      <h3>Aprofunde-se na Palavra</h3>
      <p>Os livros do Pr. Elias trazem sermões expositivos e reflexões que edificam a sua vida espiritual.</p>
      <a class="btn btn-gold" href="<?= base_url('livros') ?>">Conhecer os livros →</a>
    </aside>
  </div>
</article>

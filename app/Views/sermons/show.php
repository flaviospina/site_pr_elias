<article class="section">
  <div class="container article-page">
    <nav class="breadcrumb"><a href="<?= base_url() ?>">Início</a> / <a href="<?= base_url('sermoes') ?>">Sermões</a></nav>
    <header class="article-head">
      <?php if ($sermon['bible_reference']): ?><p class="article-ref"><?= e($sermon['bible_reference']) ?></p><?php endif; ?>
      <h1><?= e($sermon['title']) ?></h1>
      <p class="article-meta">Pr. Elias José da Silva · <?= date_br($sermon['published_at']) ?></p>
    </header>

    <?php if ($sermon['image']): ?>
    <img class="article-image" src="<?= e($sermon['image']) ?>" alt="<?= e($sermon['title']) ?>">
    <?php endif; ?>

    <?php if ($sermon['video_url']): ?>
    <p><a class="btn btn-outline" href="<?= e($sermon['video_url']) ?>" target="_blank" rel="noopener">▶ Assistir à pregação</a></p>
    <?php endif; ?>

    <div class="article-body">
      <?= $sermon['content'] /* HTML sanitizado no admin */ ?>
    </div>

    <nav class="article-nav">
      <?php if ($prev): ?><a href="<?= base_url('sermoes/' . $prev['slug']) ?>">← <?= e($prev['title']) ?></a><?php else: ?><span></span><?php endif; ?>
      <?php if ($next): ?><a href="<?= base_url('sermoes/' . $next['slug']) ?>"><?= e($next['title']) ?> →</a><?php endif; ?>
    </nav>

    <aside class="article-cta">
      <h3>Gostou desta mensagem?</h3>
      <p>Conheça os livros do Pr. Elias — sermões expositivos, devocionais e apologética para aprofundar a sua fé.</p>
      <a class="btn btn-gold" href="<?= base_url('livros') ?>">Conhecer os livros →</a>
    </aside>
  </div>
</article>

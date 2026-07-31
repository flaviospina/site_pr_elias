<?php render_banner('sobre-o-pr', [
    'kicker' => $settings['site_tagline'] ?? '',
    'title'  => 'Pr. Elias José da Silva',
]); ?>

<section class="section">
  <div class="container about-page">
    <div class="about-page-photo">
      <img src="<?= e($settings['about_image'] ?? '') ?>" alt="Pr. Elias José da Silva">
    </div>
    <div class="article-body">
      <?php if ($page): ?>
        <?= $page['content'] /* HTML sanitizado no admin */ ?>
      <?php else: ?>
        <p>Conteúdo em breve.</p>
      <?php endif; ?>
    </div>
  </div>

  <?php if ($books): ?>
  <div class="container" style="margin-top:64px">
    <div class="section-head"><h2>Obras publicadas</h2></div>
    <div class="grid grid-3">
      <?php foreach ($books as $book): ?>
      <article class="book-card">
        <a href="<?= base_url('livros/' . $book['slug']) ?>" class="book-cover">
          <img src="<?= e($book['image']) ?>" alt="Capa do livro <?= e($book['title']) ?>" loading="lazy">
        </a>
        <div class="book-info">
          <h3><a href="<?= base_url('livros/' . $book['slug']) ?>"><?= e($book['title']) ?></a></h3>
          <div class="book-buy">
            <span class="price"><strong><?= money((float) $book['price']) ?></strong></span>
            <a class="btn btn-outline" href="<?= base_url('livros/' . $book['slug']) ?>">Ver livro</a>
          </div>
        </div>
      </article>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>
</section>

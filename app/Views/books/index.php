<?php use App\Core\Csrf; ?>
<?php render_banner('livros', [
    'title'    => 'Livros do Pr. Elias',
    'subtitle' => 'Obras para fortalecer a fé, aprofundar o conhecimento das Escrituras e edificar a igreja.',
    'trust'    => [
        '🔒 Compra 100% segura',
        '✔ Garantia de ' . ($settings['guarantee_days'] ?? '7') . ' dias',
        '🚚 Envio para todo o Brasil',
    ],
]); ?>

<section class="section">
  <div class="container">
    <div class="grid grid-3">
      <?php foreach ($books as $book): ?>
      <article class="book-card">
        <a href="<?= base_url('livros/' . $book['slug']) ?>" class="book-cover">
          <?php if ($book['badge']): ?><span class="badge"><?= e($book['badge']) ?></span><?php endif; ?>
          <img src="<?= e($book['image']) ?>" alt="Capa do livro <?= e($book['title']) ?>" loading="lazy">
        </a>
        <div class="book-info">
          <h3><a href="<?= base_url('livros/' . $book['slug']) ?>"><?= e($book['title']) ?></a></h3>
          <?php if ($book['subtitle']): ?><p class="book-subtitle"><?= e($book['subtitle']) ?></p><?php endif; ?>
          <p><?= e($book['excerpt']) ?></p>
          <div class="book-buy">
            <span class="price">
              <?php $sale = $book['sale_price'] !== null && (float)$book['sale_price'] > 0 && (float)$book['sale_price'] < (float)$book['price']; ?>
              <?php if ($sale): ?><s><?= money((float) $book['price']) ?></s> <strong><?= money((float) $book['sale_price']) ?></strong>
              <?php else: ?><strong><?= money((float) $book['price']) ?></strong><?php endif; ?>
            </span>
            <?php if ((int) $book['in_stock'] === 1): ?>
            <form method="post" action="<?= base_url('carrinho/adicionar') ?>">
              <?= Csrf::field() ?>
              <input type="hidden" name="book_id" value="<?= (int) $book['id'] ?>">
              <button class="btn btn-primary" type="submit">Comprar</button>
            </form>
            <?php else: ?>
            <span class="badge badge-muted">Esgotado</span>
            <?php endif; ?>
          </div>
        </div>
      </article>
      <?php endforeach; ?>
    </div>

    <?php if ($testimonials): ?>
    <div class="section-head" style="margin-top:64px">
      <h2>O que dizem os leitores</h2>
    </div>
    <div class="grid grid-3">
      <?php foreach ($testimonials as $t): ?>
      <blockquote class="testimonial">
        <div class="stars"><?= str_repeat('★', (int) $t['rating']) . str_repeat('☆', 5 - (int) $t['rating']) ?></div>
        <p>“<?= e($t['content']) ?>”</p>
        <footer><strong><?= e($t['author']) ?></strong><?= $t['author_role'] ? ' · ' . e($t['author_role']) : '' ?></footer>
      </blockquote>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</section>

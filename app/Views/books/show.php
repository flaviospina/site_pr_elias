<?php use App\Core\Csrf; ?>
<section class="section">
  <div class="container product">
    <div class="product-media">
      <div class="product-cover">
        <?php if ($book['badge']): ?><span class="badge"><?= e($book['badge']) ?></span><?php endif; ?>
        <img src="<?= e($book['image']) ?>" alt="Capa do livro <?= e($book['title']) ?>">
      </div>
      <?php if (!empty($book['gallery'])): ?>
      <div class="product-gallery">
        <?php foreach (array_filter(array_map('trim', explode("\n", $book['gallery']))) as $img): ?>
        <img src="<?= e($img) ?>" alt="<?= e($book['title']) ?>" loading="lazy">
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </div>

    <div class="product-info">
      <nav class="breadcrumb"><a href="<?= base_url() ?>">Início</a> / <a href="<?= base_url('livros') ?>">Livros</a> / <?= e($book['title']) ?></nav>
      <h1><?= e($book['title']) ?></h1>
      <?php if ($book['subtitle']): ?><p class="product-subtitle"><?= e($book['subtitle']) ?></p><?php endif; ?>
      <p class="product-author">por <strong>Pr. Elias José da Silva</strong></p>

      <div class="product-price">
        <?php $sale = $book['sale_price'] !== null && (float)$book['sale_price'] > 0 && (float)$book['sale_price'] < (float)$book['price']; ?>
        <?php if ($sale): ?>
          <s><?= money((float) $book['price']) ?></s>
          <strong><?= money((float) $book['sale_price']) ?></strong>
          <span class="badge">Oferta</span>
        <?php else: ?>
          <strong><?= money((float) $book['price']) ?></strong>
        <?php endif; ?>
      </div>

      <?php if ((int) $book['in_stock'] === 1): ?>
      <form class="product-buy" method="post" action="<?= base_url('carrinho/adicionar') ?>">
        <?= Csrf::field() ?>
        <input type="hidden" name="book_id" value="<?= (int) $book['id'] ?>">
        <label>Quantidade
          <input type="number" name="quantity" value="1" min="1" max="99">
        </label>
        <button class="btn btn-gold btn-lg" type="submit">🛒 Adicionar ao carrinho</button>
      </form>
      <?php else: ?>
      <p class="badge badge-muted">Esgotado no momento</p>
      <?php endif; ?>

      <div class="trust-box">
        <span>🔒 <strong>Compra ultrassegura</strong> — dados protegidos por criptografia SSL</span>
        <span>✔ <strong>Garantia de <?= e($settings['guarantee_days'] ?? '7') ?> dias</strong> — devolução integral (CDC, art. 49)</span>
        <span>🚚 <strong>Envio para todo o Brasil</strong> com código de rastreio</span>
        <span>💳 Cartão, PIX e boleto pelos gateways oficiais</span>
      </div>

      <div class="product-description">
        <h2>Sobre o livro</h2>
        <?= $book['description'] /* HTML sanitizado no admin */ ?>
      </div>
    </div>
  </div>

  <?php if ($others): ?>
  <div class="container" style="margin-top:64px">
    <div class="section-head"><h2>Você também pode gostar</h2></div>
    <div class="grid grid-3">
      <?php foreach ($others as $other): ?>
      <article class="book-card">
        <a href="<?= base_url('livros/' . $other['slug']) ?>" class="book-cover">
          <img src="<?= e($other['image']) ?>" alt="Capa do livro <?= e($other['title']) ?>" loading="lazy">
        </a>
        <div class="book-info">
          <h3><a href="<?= base_url('livros/' . $other['slug']) ?>"><?= e($other['title']) ?></a></h3>
          <div class="book-buy">
            <span class="price"><strong><?= money((float) $other['price']) ?></strong></span>
            <a class="btn btn-outline" href="<?= base_url('livros/' . $other['slug']) ?>">Ver livro</a>
          </div>
        </div>
      </article>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>
</section>

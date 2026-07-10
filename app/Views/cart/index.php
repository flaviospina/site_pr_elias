<?php use App\Core\Csrf; ?>
<section class="page-hero">
  <div class="container"><h1>Carrinho</h1></div>
</section>

<section class="section">
  <div class="container">
    <?php if (!$items): ?>
      <p class="empty-state">Seu carrinho está vazio.</p>
      <p style="text-align:center"><a class="btn btn-primary btn-lg" href="<?= base_url('livros') ?>">Ver os livros →</a></p>
    <?php else: ?>
    <div class="cart-layout">
      <form method="post" action="<?= base_url('carrinho/atualizar') ?>" class="cart-table-wrap">
        <?= Csrf::field() ?>
        <table class="cart-table">
          <thead>
            <tr><th>Livro</th><th>Preço</th><th>Qtd.</th><th>Subtotal</th><th></th></tr>
          </thead>
          <tbody>
            <?php foreach ($items as $item): $book = $item['book']; ?>
            <tr>
              <td class="cart-product">
                <img src="<?= e($book['image']) ?>" alt="<?= e($book['title']) ?>">
                <a href="<?= base_url('livros/' . $book['slug']) ?>"><?= e($book['title']) ?></a>
              </td>
              <td data-label="Preço"><?= money($item['price']) ?></td>
              <td data-label="Qtd."><input type="number" name="qty[<?= (int) $book['id'] ?>]" value="<?= (int) $item['quantity'] ?>" min="0" max="99"></td>
              <td data-label="Subtotal"><strong><?= money($item['subtotal']) ?></strong></td>
              <td>
                <button class="link-button danger" type="submit" form="remove-<?= (int) $book['id'] ?>" aria-label="Remover">✕</button>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
        <div class="cart-actions">
          <a class="btn btn-outline" href="<?= base_url('livros') ?>">← Continuar comprando</a>
          <button class="btn btn-outline" type="submit">Atualizar carrinho</button>
        </div>
      </form>

      <?php foreach ($items as $item): ?>
      <form id="remove-<?= (int) $item['book']['id'] ?>" method="post" action="<?= base_url('carrinho/remover') ?>">
        <?= Csrf::field() ?>
        <input type="hidden" name="book_id" value="<?= (int) $item['book']['id'] ?>">
      </form>
      <?php endforeach; ?>

      <aside class="cart-summary">
        <h2>Resumo</h2>
        <p class="summary-row"><span>Subtotal</span><strong><?= money($total) ?></strong></p>
        <p class="summary-row"><span>Frete</span><span>calculado na entrega</span></p>
        <p class="summary-row total"><span>Total</span><strong><?= money($total) ?></strong></p>
        <a class="btn btn-gold btn-lg btn-block" href="<?= base_url('finalizar-compra') ?>">Finalizar compra →</a>
        <div class="trust-box small">
          <span>🔒 Pagamento 100% seguro</span>
          <span>✔ Garantia de <?= e($settings['guarantee_days'] ?? '7') ?> dias</span>
        </div>
      </aside>
    </div>
    <?php endif; ?>
  </div>
</section>

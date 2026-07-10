<?php use App\Core\Csrf; ?>
<form class="panel form-panel" method="post" action="<?= base_url('admin/livros/salvar') ?>">
  <?= Csrf::field() ?>
  <input type="hidden" name="id" value="<?= (int) ($book['id'] ?? 0) ?>">

  <div class="form-row">
    <label>Título *<input type="text" name="title" value="<?= e($book['title'] ?? '') ?>" required maxlength="255"></label>
    <label>Slug (URL)<input type="text" name="slug" value="<?= e($book['slug'] ?? '') ?>" placeholder="gerado automaticamente"></label>
  </div>
  <label>Subtítulo<input type="text" name="subtitle" value="<?= e($book['subtitle'] ?? '') ?>" maxlength="255"></label>
  <label>Resumo curto (vitrine)<textarea name="excerpt" rows="2"><?= e($book['excerpt'] ?? '') ?></textarea></label>
  <label>Descrição completa (aceita HTML: &lt;p&gt;, &lt;strong&gt;, &lt;ul&gt;…)<textarea name="description" rows="10"><?= e($book['description'] ?? '') ?></textarea></label>

  <div class="form-row">
    <label>Preço (R$) *<input type="text" name="price" value="<?= e($book ? number_format((float) $book['price'], 2, ',', '') : '') ?>" required></label>
    <label>Preço promocional (R$)<input type="text" name="sale_price" value="<?= e($book && $book['sale_price'] !== null ? number_format((float) $book['sale_price'], 2, ',', '') : '') ?>" placeholder="deixe vazio para não usar"></label>
    <label>Selo<input type="text" name="badge" value="<?= e($book['badge'] ?? '') ?>" placeholder="Mais vendido, Lançamento…"></label>
  </div>

  <label>Imagem da capa (URL) — envie pelo botão abaixo ou cole uma URL
    <input type="text" name="image" value="<?= e($book['image'] ?? '') ?>" data-upload-target>
  </label>
  <p><input type="file" accept="image/*" data-upload-input> <span data-upload-status></span></p>
  <label>Galeria (uma URL de imagem por linha)<textarea name="gallery" rows="3"><?= e($book['gallery'] ?? '') ?></textarea></label>

  <div class="form-row">
    <label>Nº de páginas<input type="number" name="pages" value="<?= e((string) ($book['pages'] ?? '')) ?>"></label>
    <label>ISBN<input type="text" name="isbn" value="<?= e($book['isbn'] ?? '') ?>"></label>
    <label>Estoque<input type="number" name="stock" value="<?= e((string) ($book['stock'] ?? 100)) ?>"></label>
    <label>Ordem<input type="number" name="sort_order" value="<?= e((string) ($book['sort_order'] ?? 0)) ?>"></label>
  </div>

  <div class="form-row checks">
    <label class="check"><input type="checkbox" name="in_stock" value="1" <?= !$book || $book['in_stock'] ? 'checked' : '' ?>><span>Disponível para venda</span></label>
    <label class="check"><input type="checkbox" name="featured" value="1" <?= !empty($book['featured']) ? 'checked' : '' ?>><span>Destacar na página inicial</span></label>
    <label class="check"><input type="checkbox" name="status" value="draft" <?= ($book['status'] ?? '') === 'draft' ? 'checked' : '' ?>><span>Salvar como rascunho (não aparece no site)</span></label>
  </div>

  <div class="form-actions">
    <a class="btn btn-outline" href="<?= base_url('admin/livros') ?>">Cancelar</a>
    <button class="btn btn-primary" type="submit">Salvar livro</button>
  </div>
</form>

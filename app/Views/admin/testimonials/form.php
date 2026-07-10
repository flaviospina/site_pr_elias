<?php use App\Core\Csrf; ?>
<form class="panel form-panel" method="post" action="<?= base_url('admin/depoimentos/salvar') ?>">
  <?= Csrf::field() ?>
  <input type="hidden" name="id" value="<?= (int) ($item['id'] ?? 0) ?>">

  <div class="form-row">
    <label>Nome do autor *<input type="text" name="author" value="<?= e($item['author'] ?? '') ?>" required maxlength="120"></label>
    <label>Função/descrição<input type="text" name="author_role" value="<?= e($item['author_role'] ?? '') ?>" placeholder="Pregador, Professora de EBD…"></label>
  </div>
  <label>Depoimento *<textarea name="content" rows="4" required maxlength="2000"><?= e($item['content'] ?? '') ?></textarea></label>
  <div class="form-row">
    <label>Nota (1 a 5)<input type="number" name="rating" min="1" max="5" value="<?= e((string) ($item['rating'] ?? 5)) ?>"></label>
    <label>Ordem<input type="number" name="sort_order" value="<?= e((string) ($item['sort_order'] ?? 0)) ?>"></label>
  </div>
  <label class="check"><input type="checkbox" name="status" value="draft" <?= ($item['status'] ?? '') === 'draft' ? 'checked' : '' ?>><span>Salvar como rascunho</span></label>

  <div class="form-actions">
    <a class="btn btn-outline" href="<?= base_url('admin/depoimentos') ?>">Cancelar</a>
    <button class="btn btn-primary" type="submit">Salvar depoimento</button>
  </div>
</form>

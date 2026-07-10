<?php use App\Core\Csrf; ?>
<form class="panel form-panel" method="post" action="<?= base_url('admin/paginas/salvar') ?>">
  <?= Csrf::field() ?>
  <input type="hidden" name="id" value="<?= (int) ($item['id'] ?? 0) ?>">

  <div class="form-row">
    <label>Título *<input type="text" name="title" value="<?= e($item['title'] ?? '') ?>" required maxlength="255"></label>
    <label>Slug (URL)<input type="text" name="slug" value="<?= e($item['slug'] ?? '') ?>" placeholder="gerado automaticamente"></label>
  </div>
  <label>Conteúdo (aceita HTML: &lt;p&gt;, &lt;h2&gt;, &lt;strong&gt;, &lt;ul&gt;, &lt;blockquote&gt;…)<textarea name="content" rows="20"><?= e($item['content'] ?? '') ?></textarea></label>
  <label class="check"><input type="checkbox" name="status" value="draft" <?= ($item['status'] ?? '') === 'draft' ? 'checked' : '' ?>><span>Salvar como rascunho</span></label>

  <div class="form-actions">
    <a class="btn btn-outline" href="<?= base_url('admin/paginas') ?>">Cancelar</a>
    <button class="btn btn-primary" type="submit">Salvar página</button>
  </div>
</form>

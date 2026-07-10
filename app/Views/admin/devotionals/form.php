<?php use App\Core\Csrf; ?>
<form class="panel form-panel" method="post" action="<?= base_url('admin/devocionais/salvar') ?>">
  <?= Csrf::field() ?>
  <input type="hidden" name="id" value="<?= (int) ($item['id'] ?? 0) ?>">

  <div class="form-row">
    <label>Título *<input type="text" name="title" value="<?= e($item['title'] ?? '') ?>" required maxlength="255"></label>
    <label>Referência bíblica<input type="text" name="bible_reference" value="<?= e($item['bible_reference'] ?? '') ?>" placeholder="Ex.: Salmo 46:1"></label>
  </div>
  <label>Resumo (vitrine — opcional, gerado automaticamente se vazio)<textarea name="excerpt" rows="2"><?= e($item['excerpt'] ?? '') ?></textarea></label>
  <label>Conteúdo * (aceita HTML: &lt;p&gt;, &lt;h2&gt;, &lt;strong&gt;, &lt;ul&gt;…)<textarea name="content" rows="16" required><?= e($item['content'] ?? '') ?></textarea></label>

  <div class="form-row">
    <label>Imagem de destaque (URL — opcional)<input type="text" name="image" value="<?= e($item['image'] ?? '') ?>" data-upload-target></label>
    <label>Data de publicação<input type="datetime-local" name="published_at" value="<?= e($item && $item['published_at'] ? date('Y-m-d\TH:i', strtotime($item['published_at'])) : '') ?>"></label>
  </div>
  <p><input type="file" accept="image/*" data-upload-input> <span data-upload-status></span></p>
  <label>Slug (URL)<input type="text" name="slug" value="<?= e($item['slug'] ?? '') ?>" placeholder="gerado automaticamente"></label>
  <label class="check"><input type="checkbox" name="status" value="draft" <?= ($item['status'] ?? '') === 'draft' ? 'checked' : '' ?>><span>Salvar como rascunho</span></label>

  <div class="form-actions">
    <a class="btn btn-outline" href="<?= base_url('admin/devocionais') ?>">Cancelar</a>
    <button class="btn btn-primary" type="submit">Salvar devocional</button>
  </div>
</form>

<?php use App\Core\Csrf; ?>
<form class="panel form-panel" method="post" action="<?= base_url('admin/agenda/salvar') ?>">
  <?= Csrf::field() ?>
  <input type="hidden" name="id" value="<?= (int) ($event['id'] ?? 0) ?>">

  <label>Título do evento *<input type="text" name="title" value="<?= e($event['title'] ?? '') ?>" required maxlength="255"></label>
  <label>Descrição<textarea name="description" rows="3"><?= e($event['description'] ?? '') ?></textarea></label>
  <div class="form-row">
    <label>Data *<input type="date" name="event_date" value="<?= e($event['event_date'] ?? '') ?>" required></label>
    <label>Horário<input type="text" name="event_time" value="<?= e($event['event_time'] ?? '') ?>" placeholder="19:00"></label>
  </div>
  <div class="form-row">
    <label>Local<input type="text" name="location" value="<?= e($event['location'] ?? '') ?>" placeholder="Nome da igreja/auditório"></label>
    <label>Cidade<input type="text" name="city" value="<?= e($event['city'] ?? '') ?>" placeholder="São Paulo - SP"></label>
  </div>
  <label>Link (mais informações — opcional)<input type="text" name="link" value="<?= e($event['link'] ?? '') ?>"></label>
  <label class="check"><input type="checkbox" name="status" value="draft" <?= ($event['status'] ?? '') === 'draft' ? 'checked' : '' ?>><span>Salvar como rascunho</span></label>

  <div class="form-actions">
    <a class="btn btn-outline" href="<?= base_url('admin/agenda') ?>">Cancelar</a>
    <button class="btn btn-primary" type="submit">Salvar evento</button>
  </div>
</form>

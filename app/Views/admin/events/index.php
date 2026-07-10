<?php use App\Core\Csrf; ?>
<div class="panel">
  <div class="panel-head">
    <h2>Agenda</h2>
    <a class="btn btn-primary btn-sm" href="<?= base_url('admin/agenda/novo') ?>">+ Novo evento</a>
  </div>
  <div class="table-wrap">
  <table class="admin-table">
    <thead><tr><th>Data</th><th>Evento</th><th>Local</th><th>Status</th><th class="actions">Ações</th></tr></thead>
    <tbody>
      <?php foreach ($events as $event): ?>
      <tr>
        <td><?= date_br($event['event_date']) ?><?= $event['event_time'] ? '<br><small>' . e($event['event_time']) . '</small>' : '' ?></td>
        <td><strong><?= e($event['title']) ?></strong></td>
        <td><?= e(trim(($event['location'] ? $event['location'] . ' — ' : '') . ($event['city'] ?? ''))) ?></td>
        <td><span class="status status-<?= e($event['status']) ?>"><?= $event['status'] === 'published' ? 'publicado' : 'rascunho' ?></span></td>
        <td class="actions">
          <a class="btn btn-outline btn-sm" href="<?= base_url('admin/agenda/' . (int) $event['id'] . '/editar') ?>">Editar</a>
          <form method="post" action="<?= base_url('admin/agenda/' . (int) $event['id'] . '/excluir') ?>" onsubmit="return confirm('Excluir este evento?')">
            <?= Csrf::field() ?><button class="btn btn-danger btn-sm" type="submit">Excluir</button>
          </form>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  </div>
</div>

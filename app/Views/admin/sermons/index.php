<?php use App\Core\Csrf; ?>
<div class="panel">
  <div class="panel-head">
    <h2>Sermões (<?= count($items) ?>)</h2>
    <a class="btn btn-primary btn-sm" href="<?= base_url('admin/sermoes/novo') ?>">+ Novo sermão</a>
  </div>
  <div class="table-wrap">
  <table class="admin-table">
    <thead><tr><th>Título</th><th>Publicado em</th><th>Status</th><th class="actions">Ações</th></tr></thead>
    <tbody>
      <?php foreach ($items as $item): ?>
      <tr>
        <td><strong><?= e($item['title']) ?></strong><br><small>/sermoes/<?= e($item['slug']) ?></small></td>
        <td><?= date_br($item['published_at']) ?></td>
        <td><span class="status status-<?= e($item['status']) ?>"><?= $item['status'] === 'published' ? 'publicado' : 'rascunho' ?></span></td>
        <td class="actions">
          <a class="btn btn-outline btn-sm" href="<?= base_url('admin/sermoes/' . (int) $item['id'] . '/editar') ?>">Editar</a>
          <form method="post" action="<?= base_url('admin/sermoes/' . (int) $item['id'] . '/excluir') ?>" onsubmit="return confirm('Excluir este sermão?')">
            <?= Csrf::field() ?><button class="btn btn-danger btn-sm" type="submit">Excluir</button>
          </form>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  </div>
</div>

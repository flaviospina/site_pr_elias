<?php use App\Core\Csrf; ?>
<div class="panel">
  <div class="panel-head">
    <h2>Depoimentos de leitores</h2>
    <a class="btn btn-primary btn-sm" href="<?= base_url('admin/depoimentos/novo') ?>">+ Novo depoimento</a>
  </div>
  <div class="table-wrap">
  <table class="admin-table">
    <thead><tr><th>Autor</th><th>Depoimento</th><th>Nota</th><th>Status</th><th class="actions">Ações</th></tr></thead>
    <tbody>
      <?php foreach ($items as $item): ?>
      <tr>
        <td><strong><?= e($item['author']) ?></strong><?= $item['author_role'] ? '<br><small>' . e($item['author_role']) . '</small>' : '' ?></td>
        <td><?= e(mb_strimwidth($item['content'], 0, 120, '…')) ?></td>
        <td><?= str_repeat('★', (int) $item['rating']) ?></td>
        <td><span class="status status-<?= e($item['status']) ?>"><?= $item['status'] === 'published' ? 'publicado' : 'rascunho' ?></span></td>
        <td class="actions">
          <a class="btn btn-outline btn-sm" href="<?= base_url('admin/depoimentos/' . (int) $item['id'] . '/editar') ?>">Editar</a>
          <form method="post" action="<?= base_url('admin/depoimentos/' . (int) $item['id'] . '/excluir') ?>" onsubmit="return confirm('Excluir este depoimento?')">
            <?= Csrf::field() ?><button class="btn btn-danger btn-sm" type="submit">Excluir</button>
          </form>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  </div>
</div>

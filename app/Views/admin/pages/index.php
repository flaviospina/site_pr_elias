<?php use App\Core\Csrf; ?>
<div class="panel">
  <div class="panel-head">
    <h2>Páginas do site</h2>
    <a class="btn btn-primary btn-sm" href="<?= base_url('admin/paginas/novo') ?>">+ Nova página</a>
  </div>
  <div class="table-wrap">
  <table class="admin-table">
    <thead><tr><th>Título</th><th>URL</th><th>Atualizada em</th><th>Status</th><th class="actions">Ações</th></tr></thead>
    <tbody>
      <?php foreach ($items as $item): ?>
      <tr>
        <td><strong><?= e($item['title']) ?></strong></td>
        <td><small>/pagina/<?= e($item['slug']) ?></small></td>
        <td><?= date_br($item['updated_at'], true) ?></td>
        <td><span class="status status-<?= e($item['status']) ?>"><?= $item['status'] === 'published' ? 'publicada' : 'rascunho' ?></span></td>
        <td class="actions">
          <a class="btn btn-outline btn-sm" href="<?= base_url('admin/paginas/' . (int) $item['id'] . '/editar') ?>">Editar</a>
          <form method="post" action="<?= base_url('admin/paginas/' . (int) $item['id'] . '/excluir') ?>" onsubmit="return confirm('Excluir esta página?')">
            <?= Csrf::field() ?><button class="btn btn-danger btn-sm" type="submit">Excluir</button>
          </form>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  </div>
  <p style="margin-top:12px"><small>As páginas <strong>sobre-o-pr</strong>, <strong>politica-de-privacidade</strong> e <strong>termos-e-condicoes</strong> são usadas pelo site — edite o conteúdo, mas evite excluí-las ou alterar o slug.</small></p>
</div>

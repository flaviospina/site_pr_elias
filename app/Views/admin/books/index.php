<?php use App\Core\Csrf; ?>
<div class="panel">
  <div class="panel-head">
    <h2>Livros da loja</h2>
    <a class="btn btn-primary btn-sm" href="<?= base_url('admin/livros/novo') ?>">+ Novo livro</a>
  </div>
  <div class="table-wrap">
  <table class="admin-table">
    <thead><tr><th>Capa</th><th>Título</th><th>Preço</th><th>Destaque</th><th>Status</th><th class="actions">Ações</th></tr></thead>
    <tbody>
      <?php foreach ($books as $book): ?>
      <tr>
        <td><?php if ($book['image']): ?><img class="thumb" src="<?= e($book['image']) ?>" alt=""><?php endif; ?></td>
        <td><strong><?= e($book['title']) ?></strong><br><small>/livros/<?= e($book['slug']) ?></small></td>
        <td><?= money((float) $book['price']) ?><?= $book['sale_price'] ? '<br><small>promo: ' . money((float) $book['sale_price']) . '</small>' : '' ?></td>
        <td><?= $book['featured'] ? '⭐' : '—' ?></td>
        <td><span class="status status-<?= e($book['status']) ?>"><?= $book['status'] === 'published' ? 'publicado' : 'rascunho' ?></span></td>
        <td class="actions">
          <a class="btn btn-outline btn-sm" href="<?= base_url('admin/livros/' . (int) $book['id'] . '/editar') ?>">Editar</a>
          <form method="post" action="<?= base_url('admin/livros/' . (int) $book['id'] . '/excluir') ?>" onsubmit="return confirm('Excluir este livro?')">
            <?= Csrf::field() ?><button class="btn btn-danger btn-sm" type="submit">Excluir</button>
          </form>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  </div>
</div>

<?php use App\Core\Csrf; ?>
<div class="panel">
  <div class="panel-head"><h2>Inscritos nos devocionais (<?= count($subscribers) ?>)</h2></div>
  <?php if (!$subscribers): ?>
    <p class="empty-state">Nenhum inscrito ainda.</p>
  <?php else: ?>
  <div class="table-wrap">
  <table class="admin-table">
    <thead><tr><th>Nome</th><th>E-mail</th><th>Inscrito em</th><th class="actions">Ações</th></tr></thead>
    <tbody>
      <?php foreach ($subscribers as $s): ?>
      <tr>
        <td><?= e($s['name'] ?? '—') ?></td>
        <td><?= e($s['email']) ?></td>
        <td><?= date_br($s['created_at'], true) ?></td>
        <td class="actions">
          <form method="post" action="<?= base_url('admin/inscritos/' . (int) $s['id'] . '/excluir') ?>" onsubmit="return confirm('Remover este inscrito?')">
            <?= Csrf::field() ?><button class="btn btn-danger btn-sm" type="submit">Remover</button>
          </form>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  </div>
  <p style="margin-top:12px"><small>Dica: para enviar os devocionais em massa, exporte esta lista para a sua ferramenta de e-mail preferida.</small></p>
  <?php endif; ?>
</div>

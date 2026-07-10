<?php use App\Core\Csrf; ?>
<div class="panel">
  <div class="panel-head">
    <h2>Usuários do painel</h2>
    <a class="btn btn-primary btn-sm" href="<?= base_url('admin/usuarios/novo') ?>">+ Novo usuário</a>
  </div>
  <div class="table-wrap">
  <table class="admin-table">
    <thead><tr><th>Nome</th><th>E-mail</th><th>Perfil</th><th>Ativo</th><th>Último acesso</th><th class="actions">Ações</th></tr></thead>
    <tbody>
      <?php foreach ($users as $u): ?>
      <tr>
        <td><strong><?= e($u['name']) ?></strong></td>
        <td><?= e($u['email']) ?></td>
        <td><?= $u['role'] === 'admin' ? 'Administrador' : 'Editor' ?></td>
        <td><?= $u['active'] ? '✅' : '🚫' ?></td>
        <td><?= $u['last_login_at'] ? date_br($u['last_login_at'], true) : '—' ?></td>
        <td class="actions">
          <a class="btn btn-outline btn-sm" href="<?= base_url('admin/usuarios/' . (int) $u['id'] . '/editar') ?>">Editar</a>
          <form method="post" action="<?= base_url('admin/usuarios/' . (int) $u['id'] . '/excluir') ?>" onsubmit="return confirm('Excluir este usuário?')">
            <?= Csrf::field() ?><button class="btn btn-danger btn-sm" type="submit">Excluir</button>
          </form>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  </div>
</div>

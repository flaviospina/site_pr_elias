<div class="panel">
  <div class="panel-head">
    <h2>Banners das páginas</h2>
  </div>
  <p class="help-note">Cada página do site tem um banner no topo (logo abaixo do menu). Clique em "Configurar" para trocar a imagem de fundo, o texto, os botões e o estilo. Se não configurar, a página exibe o texto padrão.</p>
  <div class="table-wrap">
  <table class="admin-table">
    <thead><tr><th>Página</th><th>Prévia</th><th>Título atual</th><th>Situação</th><th class="actions">Ações</th></tr></thead>
    <tbody>
      <?php foreach ($banners as $item): $d = $item['data']; ?>
      <tr>
        <td><strong><?= e($item['label']) ?></strong></td>
        <td><?php if (!empty($d['image'])): ?><img class="thumb" style="width:80px;height:44px;object-fit:cover" src="<?= e($d['image']) ?>" alt=""><?php else: ?><span style="color:var(--muted)">—</span><?php endif; ?></td>
        <td><?= e($d['title'] ?? '') ?: '<span style="color:var(--muted)">(padrão da página)</span>' ?></td>
        <td>
          <?php if (!$d): ?><span class="status status-draft">não configurado</span>
          <?php elseif ((int) $d['enabled'] === 1): ?><span class="status status-published">ativo</span>
          <?php else: ?><span class="status status-cancelled">desativado</span><?php endif; ?>
        </td>
        <td class="actions">
          <a class="btn btn-primary btn-sm" href="<?= base_url('admin/banners/' . e($item['location']) . '/editar') ?>">Configurar</a>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  </div>
</div>

<div class="panel">
  <div class="panel-head">
    <h2>Pedidos</h2>
    <nav class="filter-tabs">
      <a class="<?= !$filter ? 'active' : '' ?>" href="<?= base_url('admin/pedidos') ?>">Todos</a>
      <a class="<?= $filter === 'pending' ? 'active' : '' ?>" href="<?= base_url('admin/pedidos?status=pending') ?>">Aguardando</a>
      <a class="<?= $filter === 'paid' ? 'active' : '' ?>" href="<?= base_url('admin/pedidos?status=paid') ?>">Pagos</a>
      <a class="<?= $filter === 'shipped' ? 'active' : '' ?>" href="<?= base_url('admin/pedidos?status=shipped') ?>">Enviados</a>
      <a class="<?= $filter === 'completed' ? 'active' : '' ?>" href="<?= base_url('admin/pedidos?status=completed') ?>">Concluídos</a>
      <a class="<?= $filter === 'cancelled' ? 'active' : '' ?>" href="<?= base_url('admin/pedidos?status=cancelled') ?>">Cancelados</a>
    </nav>
  </div>
  <?php if (!$orders): ?>
    <p class="empty-state">Nenhum pedido encontrado.</p>
  <?php else: ?>
  <div class="table-wrap">
  <table class="admin-table">
    <thead><tr><th>Pedido</th><th>Cliente</th><th>Contato</th><th>Total</th><th>Pagamento</th><th>Status</th><th>Data</th></tr></thead>
    <tbody>
      <?php foreach ($orders as $o): ?>
      <tr>
        <td><a href="<?= base_url('admin/pedidos/' . (int) $o['id']) ?>"><strong><?= e($o['order_code']) ?></strong></a></td>
        <td><?= e($o['customer_name']) ?></td>
        <td><small><?= e($o['customer_email']) ?><?= $o['customer_phone'] ? '<br>' . e($o['customer_phone']) : '' ?></small></td>
        <td><?= money((float) $o['total']) ?></td>
        <td><?= e($o['payment_method'] ?? '—') ?></td>
        <td><span class="status status-<?= e($o['status']) ?>"><?= e($o['status']) ?></span></td>
        <td><?= date_br($o['created_at'], true) ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  </div>
  <?php endif; ?>
</div>

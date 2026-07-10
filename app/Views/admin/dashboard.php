<div class="stat-grid">
  <div class="stat-card">
    <span class="stat-value"><?= money((float) $orderStats['revenue']) ?></span>
    <span class="stat-label">Receita (pedidos pagos)</span>
  </div>
  <div class="stat-card">
    <span class="stat-value"><?= (int) $orderStats['total_orders'] ?></span>
    <span class="stat-label">Pedidos no total</span>
  </div>
  <div class="stat-card <?= (int) $orderStats['pending_orders'] > 0 ? 'attention' : '' ?>">
    <span class="stat-value"><?= (int) $orderStats['pending_orders'] ?></span>
    <span class="stat-label">Pedidos aguardando</span>
  </div>
  <div class="stat-card <?= $unreadMsgs > 0 ? 'attention' : '' ?>">
    <span class="stat-value"><?= (int) $unreadMsgs ?></span>
    <span class="stat-label">Mensagens não lidas</span>
  </div>
  <div class="stat-card">
    <span class="stat-value"><?= (int) $subscribers ?></span>
    <span class="stat-label">Inscritos nos devocionais</span>
  </div>
  <div class="stat-card">
    <span class="stat-value"><?= (int) $counts['books'] ?> · <?= (int) $counts['sermons'] ?> · <?= (int) $counts['devotionals'] ?></span>
    <span class="stat-label">Livros · Sermões · Devocionais</span>
  </div>
</div>

<div class="panel">
  <div class="panel-head">
    <h2>Últimos pedidos</h2>
    <a class="btn btn-outline btn-sm" href="<?= base_url('admin/pedidos') ?>">Ver todos</a>
  </div>
  <?php if (!$recentOrders): ?>
    <p class="empty-state">Nenhum pedido ainda.</p>
  <?php else: ?>
  <div class="table-wrap">
  <table class="admin-table">
    <thead><tr><th>Pedido</th><th>Cliente</th><th>Total</th><th>Pagamento</th><th>Status</th><th>Data</th></tr></thead>
    <tbody>
      <?php foreach ($recentOrders as $o): ?>
      <tr>
        <td><a href="<?= base_url('admin/pedidos/' . (int) $o['id']) ?>"><?= e($o['order_code']) ?></a></td>
        <td><?= e($o['customer_name']) ?></td>
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

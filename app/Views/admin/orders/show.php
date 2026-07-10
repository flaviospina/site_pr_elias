<?php use App\Core\Csrf;
$statusLabels = ['pending' => 'Aguardando pagamento', 'paid' => 'Pago', 'shipped' => 'Enviado', 'completed' => 'Concluído', 'cancelled' => 'Cancelado'];
?>
<div class="panel">
  <div class="panel-head">
    <h2>Pedido <?= e($order['order_code']) ?> <span class="status status-<?= e($order['status']) ?>"><?= e($statusLabels[$order['status']] ?? $order['status']) ?></span></h2>
    <a class="btn btn-outline btn-sm" href="<?= base_url('admin/pedidos') ?>">← Voltar</a>
  </div>

  <div class="order-grid">
    <div>
      <h3>Cliente</h3>
      <p><strong><?= e($order['customer_name']) ?></strong><br>
      <?= e($order['customer_email']) ?><br>
      <?= $order['customer_phone'] ? 'WhatsApp: ' . e($order['customer_phone']) . '<br>' : '' ?>
      <?= $order['customer_cpf'] ? 'CPF: ' . e($order['customer_cpf']) : '' ?></p>
      <?php if ($order['customer_phone']): ?>
      <a class="btn btn-outline btn-sm" target="_blank" rel="noopener" href="https://wa.me/55<?= e(preg_replace('/\D/', '', $order['customer_phone'])) ?>?text=<?= rawurlencode('Olá ' . $order['customer_name'] . '! Sobre o seu pedido ' . $order['order_code'] . '…') ?>">Chamar no WhatsApp</a>
      <?php endif; ?>
    </div>
    <div>
      <h3>Entrega</h3>
      <p><?= e($order['address_street']) ?>, <?= e($order['address_number']) ?>
      <?= $order['address_complement'] ? ' — ' . e($order['address_complement']) : '' ?><br>
      <?= e($order['address_district']) ?> — <?= e($order['address_city']) ?>/<?= e($order['address_state']) ?><br>
      CEP: <?= e($order['address_zip']) ?></p>
    </div>
    <div>
      <h3>Pagamento</h3>
      <p>Método: <strong><?= e($order['payment_method'] ?? '—') ?></strong><br>
      <?= $order['payment_ref'] ? 'Ref.: ' . e($order['payment_ref']) . '<br>' : '' ?>
      Data: <?= date_br($order['created_at'], true) ?></p>
      <?php if ($order['notes']): ?><p><strong>Observações:</strong> <?= e($order['notes']) ?></p><?php endif; ?>
    </div>
  </div>

  <h3 style="margin-top:24px">Itens</h3>
  <div class="table-wrap">
  <table class="admin-table">
    <thead><tr><th>Livro</th><th>Preço un.</th><th>Qtd.</th><th>Subtotal</th></tr></thead>
    <tbody>
      <?php foreach ($items as $item): ?>
      <tr>
        <td><?= e($item['title']) ?></td>
        <td><?= money((float) $item['unit_price']) ?></td>
        <td><?= (int) $item['quantity'] ?></td>
        <td><?= money((float) $item['line_total']) ?></td>
      </tr>
      <?php endforeach; ?>
      <tr>
        <td colspan="3" style="text-align:right"><strong>Frete</strong></td>
        <td><?= money((float) $order['shipping']) ?></td>
      </tr>
      <tr>
        <td colspan="3" style="text-align:right"><strong>Total</strong></td>
        <td><strong><?= money((float) $order['total']) ?></strong></td>
      </tr>
    </tbody>
  </table>
  </div>

  <div class="form-actions" style="margin-top:24px;justify-content:space-between">
    <form method="post" action="<?= base_url('admin/pedidos/' . (int) $order['id'] . '/status') ?>" style="display:flex;gap:8px;align-items:center">
      <?= Csrf::field() ?>
      <label style="margin:0">Alterar status:
        <select name="status">
          <?php foreach ($statusLabels as $value => $label): ?>
          <option value="<?= $value ?>" <?= $order['status'] === $value ? 'selected' : '' ?>><?= $label ?></option>
          <?php endforeach; ?>
        </select>
      </label>
      <button class="btn btn-primary btn-sm" type="submit">Salvar</button>
    </form>
    <form method="post" action="<?= base_url('admin/pedidos/' . (int) $order['id'] . '/excluir') ?>" onsubmit="return confirm('Excluir este pedido definitivamente?')">
      <?= Csrf::field() ?>
      <button class="btn btn-danger btn-sm" type="submit">Excluir pedido</button>
    </form>
  </div>
</div>

<?php
namespace App\Controllers\Admin;

use App\Models\Order;

class OrderController extends AdminController
{
    public function index(): void
    {
        $status = $_GET['status'] ?? null;
        $valid  = ['pending','paid','shipped','completed','cancelled'];
        if (!in_array($status, $valid, true)) $status = null;

        $this->adminView('admin/orders/index', [
            'pageTitle' => 'Pedidos',
            'orders'    => Order::allForAdmin($status),
            'filter'    => $status,
        ]);
    }

    public function show(string $id): void
    {
        $order = Order::find((int) $id);
        if (!$order) redirect('admin/pedidos');
        $this->adminView('admin/orders/show', [
            'pageTitle' => 'Pedido ' . $order['order_code'],
            'order'     => $order,
            'items'     => Order::items((int) $id),
        ]);
    }

    /** Consulta o Mercado Pago e confirma o pedido se o pagamento foi aprovado. */
    public function verifyMp(string $id): void
    {
        $this->requireCsrf();
        $order = Order::find((int) $id);
        if (!$order) redirect('admin/pedidos');

        $result = \App\Core\Payment::settleMercadoPagoOrder($order);
        flash($result['settled'] ? 'success' : 'error', 'Mercado Pago: ' . $result['message']);
        redirect('admin/pedidos/' . (int) $id);
    }

    public function updateStatus(string $id): void
    {
        $this->requireCsrf();
        $order     = Order::find((int) $id);
        $newStatus = $_POST['status'] ?? '';
        if (!$order) redirect('admin/pedidos');

        $wasPaid = in_array($order['status'], ['paid', 'shipped', 'completed'], true);
        Order::updateStatus((int) $id, $newStatus);

        // Ao confirmar o pagamento manualmente (ex.: PIX após receber o
        // comprovante), avisa o cliente por e-mail e o admin pelo Telegram.
        if ($newStatus === 'paid' && !$wasPaid) {
            $fresh = Order::find((int) $id);
            \App\Core\Mailer::orderPaid($fresh, Order::items((int) $id));
            \App\Core\Notifier::orderPaid($fresh);
            flash('success', 'Pedido confirmado como PAGO. O cliente foi avisado por e-mail.');
        } else {
            flash('success', 'Status do pedido atualizado.');
        }
        redirect('admin/pedidos/' . (int) $id);
    }

    public function delete(string $id): void
    {
        $this->requireCsrf();
        Order::delete((int) $id);
        flash('success', 'Pedido excluído.');
        redirect('admin/pedidos');
    }
}

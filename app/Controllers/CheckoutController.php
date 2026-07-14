<?php
namespace App\Controllers;

use App\Core\Cart;
use App\Core\Controller;
use App\Core\Payment;
use App\Models\Order;
use App\Models\Setting;

class CheckoutController extends Controller
{
    public function index(): void
    {
        $items = Cart::items();
        if (!$items) {
            flash('error', 'Seu carrinho está vazio.');
            redirect('livros');
        }
        $this->view('checkout/index', [
            'pageTitle' => 'Finalizar Compra',
            'items'     => $items,
            'subtotal'  => Cart::total(),
            'shipping'  => (float) Setting::get('shipping_flat', '0'),
            'methods'   => Payment::enabledMethods(),
        ]);
    }

    public function place(): void
    {
        $this->requireCsrf();

        $items = Cart::items();
        if (!$items) {
            redirect('livros');
        }

        // Validação
        $required = ['customer_name', 'customer_email', 'address_zip', 'address_street',
                     'address_number', 'address_district', 'address_city', 'address_state'];
        $errors = [];
        foreach ($required as $field) {
            if (trim($_POST[$field] ?? '') === '') $errors[] = $field;
        }
        $email = trim($_POST['customer_email'] ?? '');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'customer_email';
        if (empty($_POST['lgpd_consent'])) $errors[] = 'lgpd_consent';

        $methods = Payment::enabledMethods();
        $method  = $_POST['payment_method'] ?? '';
        if (!isset($methods[$method])) $errors[] = 'payment_method';

        if ($errors) {
            $_SESSION['_old'] = $_POST;
            flash('error', 'Preencha todos os campos obrigatórios, aceite a política de privacidade e escolha a forma de pagamento.');
            redirect('finalizar-compra');
        }

        $subtotal = Cart::total();
        $shipping = (float) Setting::get('shipping_flat', '0');

        $order = Order::create([
            'customer_name'      => mb_substr(trim($_POST['customer_name']), 0, 160),
            'customer_email'     => mb_substr($email, 0, 190),
            'customer_phone'     => mb_substr(trim($_POST['customer_phone'] ?? ''), 0, 30) ?: null,
            'customer_cpf'       => mb_substr(preg_replace('/[^0-9]/', '', $_POST['customer_cpf'] ?? ''), 0, 14) ?: null,
            'address_zip'        => mb_substr(trim($_POST['address_zip']), 0, 12),
            'address_street'     => mb_substr(trim($_POST['address_street']), 0, 255),
            'address_number'     => mb_substr(trim($_POST['address_number']), 0, 20),
            'address_complement' => mb_substr(trim($_POST['address_complement'] ?? ''), 0, 120) ?: null,
            'address_district'   => mb_substr(trim($_POST['address_district']), 0, 120),
            'address_city'       => mb_substr(trim($_POST['address_city']), 0, 120),
            'address_state'      => mb_substr(strtoupper(trim($_POST['address_state'])), 0, 2),
            'subtotal'           => $subtotal,
            'shipping'           => $shipping,
            'total'              => $subtotal + $shipping,
            'payment_method'     => $method,
            'notes'              => mb_substr(trim($_POST['notes'] ?? ''), 0, 2000) ?: null,
            'lgpd_consent'       => 1,
        ], $items);

        Cart::clear();
        unset($_SESSION['_old']);

        // E-mails: confirmação para o cliente + aviso para o administrador
        \App\Core\Mailer::orderReceived($order, Order::items((int) $order['id']));

        // Mercado Pago: redireciona para o checkout seguro do gateway
        if ($method === 'mercadopago') {
            $orderItems = Order::items((int) $order['id']);
            $url = Payment::mercadoPagoCheckout($order, $orderItems);
            if ($url) {
                Order::setPaymentRef((int) $order['id'], 'mp:redirected');
                redirect($url);
            }
            // Falha na API → segue para a página de confirmação com instruções
        }

        redirect('pedido/' . $order['order_code']);
    }

    public function confirmation(string $code): void
    {
        $order = Order::findByCode($code);
        if (!$order) {
            (new PageController())->notFound();
            return;
        }

        // Pedido Mercado Pago ainda pendente: confere na API se já foi aprovado.
        // (confirmação segura, feita servidor-a-servidor — nunca pela URL)
        if ($order['payment_method'] === 'mercadopago' && $order['status'] === 'pending') {
            $paymentId = $_GET['payment_id'] ?? $_GET['collection_id'] ?? null;
            $payment   = $paymentId ? Payment::mpGetPayment((string) $paymentId) : null;
            $result    = Payment::settleMercadoPagoOrder($order, $payment);
            if ($result['settled']) {
                $order = Order::findByCode($code); // recarrega com o novo status
            }
        }

        $this->view('checkout/confirmation', [
            'pageTitle'     => 'Pedido ' . $order['order_code'],
            'order'         => $order,
            'items'         => Order::items((int) $order['id']),
            'gatewayReturn' => isset($_GET['pago']) ? 'success' : (isset($_GET['falha']) ? 'failure' : null),
        ]);
    }

    /**
     * Webhook do Mercado Pago: chamado automaticamente pelo gateway quando o
     * pagamento muda de situação. Confirma o pedido sem depender do cliente
     * voltar ao site.
     */
    public function mpWebhook(): void
    {
        $raw  = file_get_contents('php://input') ?: '';
        $body = json_decode($raw, true) ?: [];

        // Formatos aceitos: JSON {type:"payment",data:{id}} e query ?topic=payment&id=...
        $type      = $body['type'] ?? $_GET['type'] ?? $_GET['topic'] ?? '';
        $paymentId = $body['data']['id'] ?? $_GET['data_id'] ?? $_GET['id'] ?? null;

        if ($paymentId && ($type === 'payment' || $type === '')) {
            $payment = Payment::mpGetPayment((string) $paymentId);
            if ($payment && !empty($payment['external_reference'])) {
                $order = Order::findByCode($payment['external_reference']);
                if ($order) {
                    Payment::settleMercadoPagoOrder($order, $payment);
                }
            }
        }

        // Sempre responde 200 para o Mercado Pago não reenviar indefinidamente
        http_response_code(200);
        header('Content-Type: text/plain');
        exit('ok');
    }
}

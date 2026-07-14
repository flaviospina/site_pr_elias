<?php
namespace App\Core;

use App\Models\Setting;

/**
 * Integração com gateways de pagamento brasileiros.
 * As credenciais são configuradas no Painel > Configurações > Pagamentos.
 */
class Payment
{
    /** Lista os métodos de pagamento habilitados no painel. */
    public static function enabledMethods(): array
    {
        $methods = [];
        if (Setting::get('pay_mercadopago_enabled') === '1' && Setting::get('pay_mercadopago_access_token')) {
            $methods['mercadopago'] = [
                'label' => 'Cartão, PIX ou Boleto (Mercado Pago)',
                'hint'  => 'Pagamento processado em ambiente seguro do Mercado Pago.',
            ];
        }
        if (Setting::get('pay_pix_enabled') === '1' && Setting::get('pay_pix_key')) {
            $methods['pix'] = [
                'label' => 'PIX direto',
                'hint'  => 'Transferência instantânea. Você recebe a chave após confirmar o pedido.',
            ];
        }
        if (Setting::get('pay_pagseguro_enabled') === '1' && Setting::get('pay_pagseguro_token')) {
            $methods['pagseguro'] = [
                'label' => 'PagBank (PagSeguro)',
                'hint'  => 'Pagamento processado em ambiente seguro do PagBank.',
            ];
        }
        if (Setting::get('pay_paypal_enabled') === '1' && Setting::get('pay_paypal_client_id')) {
            $methods['paypal'] = [
                'label' => 'PayPal',
                'hint'  => 'Pague com sua conta PayPal ou cartão internacional.',
            ];
        }
        if (Setting::get('pay_whatsapp_enabled') === '1') {
            $methods['whatsapp'] = [
                'label' => 'Combinar pelo WhatsApp',
                'hint'  => 'Finalize o pedido e combine o pagamento diretamente com a nossa equipe.',
            ];
        }
        return $methods;
    }

    /**
     * Cria uma preferência de pagamento no Mercado Pago (Checkout Pro)
     * e retorna a URL de pagamento, ou null em caso de falha.
     */
    public static function mercadoPagoCheckout(array $order, array $items): ?string
    {
        $token = Setting::get('pay_mercadopago_access_token');
        if (!$token) return null;

        $mpItems = [];
        foreach ($items as $item) {
            $mpItems[] = [
                'title'       => $item['title'],
                'quantity'    => (int) $item['quantity'],
                'unit_price'  => (float) $item['unit_price'],
                'currency_id' => 'BRL',
            ];
        }
        if ((float) $order['shipping'] > 0) {
            $mpItems[] = [
                'title'       => 'Frete',
                'quantity'    => 1,
                'unit_price'  => (float) $order['shipping'],
                'currency_id' => 'BRL',
            ];
        }

        $payload = [
            'items'              => $mpItems,
            'external_reference' => $order['order_code'],
            'payer'              => [
                'name'  => $order['customer_name'],
                'email' => $order['customer_email'],
            ],
            'back_urls' => [
                'success' => base_url('pedido/' . $order['order_code'] . '?pago=1'),
                'pending' => base_url('pedido/' . $order['order_code']),
                'failure' => base_url('pedido/' . $order['order_code'] . '?falha=1'),
            ],
            'auto_return'       => 'approved',
            'statement_descriptor' => 'LIVROS PR ELIAS',
        ];

        // Webhook: o Mercado Pago avisa o site automaticamente quando o
        // pagamento é aprovado (exige URL pública em HTTPS)
        $webhook = base_url('webhook/mercadopago');
        if (str_starts_with($webhook, 'https://')) {
            $payload['notification_url'] = $webhook;
        }

        $response = self::httpPost(
            'https://api.mercadopago.com/checkout/preferences',
            json_encode($payload),
            ['Authorization: Bearer ' . $token, 'Content-Type: application/json']
        );
        if (!$response) return null;

        // Sempre usa o init_point oficial. O antigo "sandbox_init_point" foi
        // descontinuado pelo Mercado Pago: para testar, usam-se credenciais de
        // TESTE (TEST-...) com este mesmo link.
        $data = json_decode($response, true);
        return $data['init_point'] ?? null;
    }

    /** Consulta um pagamento específico na API do Mercado Pago. */
    public static function mpGetPayment(string $paymentId): ?array
    {
        $token = Setting::get('pay_mercadopago_access_token');
        if (!$token || !preg_match('/^\d+$/', $paymentId)) return null;
        $data = self::httpGet('https://api.mercadopago.com/v1/payments/' . $paymentId, $token);
        return is_array($data) && isset($data['status']) ? $data : null;
    }

    /** Busca o pagamento mais recente de um pedido pela referência externa. */
    public static function mpFindPaymentByReference(string $orderCode): ?array
    {
        $token = Setting::get('pay_mercadopago_access_token');
        if (!$token) return null;
        $data = self::httpGet(
            'https://api.mercadopago.com/v1/payments/search?sort=date_created&criteria=desc&external_reference=' . rawurlencode($orderCode),
            $token
        );
        $results = $data['results'] ?? [];
        if (!$results) return null;
        // Prefere um pagamento aprovado; senão, o mais recente
        foreach ($results as $payment) {
            if (($payment['status'] ?? '') === 'approved') return $payment;
        }
        return $results[0];
    }

    /**
     * Confere na API do Mercado Pago se o pedido foi pago e, em caso positivo,
     * marca como pago e dispara os e-mails. Seguro: valida referência e valor.
     * Retorna [settled => bool, message => string].
     */
    public static function settleMercadoPagoOrder(array $order, ?array $payment = null): array
    {
        if ($order['status'] !== 'pending') {
            return ['settled' => true, 'message' => 'O pedido já está confirmado.'];
        }
        $payment ??= self::mpFindPaymentByReference($order['order_code']);
        if (!$payment) {
            return ['settled' => false, 'message' => 'Nenhum pagamento encontrado no Mercado Pago para este pedido.'];
        }
        if (($payment['external_reference'] ?? '') !== $order['order_code']) {
            return ['settled' => false, 'message' => 'O pagamento encontrado não pertence a este pedido.'];
        }
        $status = $payment['status'] ?? '';
        if ($status !== 'approved') {
            $map = ['pending' => 'pendente', 'in_process' => 'em análise', 'rejected' => 'recusado', 'cancelled' => 'cancelado', 'refunded' => 'estornado'];
            return ['settled' => false, 'message' => 'Situação do pagamento no Mercado Pago: ' . ($map[$status] ?? $status) . '.'];
        }
        if (abs((float) ($payment['transaction_amount'] ?? 0) - (float) $order['total']) > 0.01) {
            return ['settled' => false, 'message' => 'O valor pago não confere com o total do pedido.'];
        }

        \App\Models\Order::updateStatus((int) $order['id'], 'paid');
        \App\Models\Order::setPaymentRef((int) $order['id'], 'mp:' . ($payment['id'] ?? ''));
        $fresh = \App\Models\Order::find((int) $order['id']);
        Mailer::orderPaid($fresh, \App\Models\Order::items((int) $order['id']));
        Notifier::orderPaid($fresh);
        return ['settled' => true, 'message' => 'Pagamento aprovado confirmado pelo Mercado Pago.'];
    }

    private static function httpGet(string $url, string $token): ?array
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => ['Authorization: Bearer ' . $token],
            CURLOPT_TIMEOUT        => 15,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);
        $response = curl_exec($ch);
        $status   = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        curl_close($ch);
        if ($response === false || $status < 200 || $status >= 300) return null;
        $data = json_decode($response, true);
        return is_array($data) ? $data : null;
    }

    /**
     * Valida um Access Token do Mercado Pago consultando a própria conta.
     * Retorna [ok => bool, message => string].
     */
    public static function mpValidateToken(string $token): array
    {
        $ch = curl_init('https://api.mercadopago.com/users/me');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => ['Authorization: Bearer ' . $token],
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);
        $response = curl_exec($ch);
        $status   = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        curl_close($ch);

        if ($response === false) {
            return ['ok' => false, 'message' => 'Não foi possível contatar o Mercado Pago (verifique a conexão do servidor).'];
        }
        if ($status !== 200) {
            return ['ok' => false, 'message' => 'O Mercado Pago recusou o Access Token (HTTP ' . $status . '). Confira se copiou o token completo e da aba certa (teste ou produção).'];
        }
        $me = json_decode($response, true);
        $who = trim(($me['first_name'] ?? '') . ' ' . ($me['last_name'] ?? '')) ?: ($me['email'] ?? 'conta');
        return ['ok' => true, 'message' => 'Token válido — conta: ' . $who . '.'];
    }

    private static function httpPost(string $url, string $body, array $headers): ?string
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $body,
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_TIMEOUT        => 20,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);
        $response = curl_exec($ch);
        $status   = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        curl_close($ch);
        return ($response !== false && $status >= 200 && $status < 300) ? $response : null;
    }
}

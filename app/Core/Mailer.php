<?php
namespace App\Core;

use App\Models\Setting;

/**
 * Envio de e-mails transacionais via mail() do PHP (padrão em hospedagem cPanel).
 * Dica de entregabilidade: crie a caixa postal do remetente (ex.:
 * contato@seudominio.com.br) no cPanel para os avisos não caírem em spam.
 */
class Mailer
{
    public static function send(string $to, string $subject, string $html): bool
    {
        if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
            return false;
        }
        $siteName = Setting::get('site_name', 'Site');
        $host     = preg_replace('/^www\./', '', $_SERVER['HTTP_HOST'] ?? 'localhost');
        $from     = Setting::get('contact_email') ?: ('no-reply@' . $host);

        $headers = implode("\r\n", [
            'MIME-Version: 1.0',
            'Content-Type: text/html; charset=UTF-8',
            'From: =?UTF-8?B?' . base64_encode($siteName) . "?= <{$from}>",
            'Reply-To: ' . $from,
        ]);

        try {
            return @mail($to, '=?UTF-8?B?' . base64_encode($subject) . '?=', $html, $headers);
        } catch (\Throwable) {
            return false;
        }
    }

    /** E-mail ao cliente: pedido recebido (enviado na criação do pedido). */
    public static function orderReceived(array $order, array $items): void
    {
        $html = self::orderHtml(
            'Recebemos o seu pedido!',
            'Obrigado pela sua compra, ' . e($order['customer_name']) . '! Seu pedido foi registrado e está sendo processado. Guarde o número dele para acompanhamento.',
            $order, $items
        );
        self::send($order['customer_email'], 'Pedido ' . $order['order_code'] . ' recebido — ' . Setting::get('site_name', ''), $html);

        // Aviso ao administrador
        $admin = Setting::get('contact_email');
        if ($admin) {
            self::send($admin, '🛒 Novo pedido ' . $order['order_code'] . ' — ' . money((float) $order['total']),
                self::orderHtml('Novo pedido na loja', 'Um novo pedido foi realizado no site. Acesse o painel administrativo para ver os detalhes.', $order, $items, true));
        }
    }

    /** E-mail ao cliente: pagamento aprovado. */
    public static function orderPaid(array $order, array $items): void
    {
        $html = self::orderHtml(
            '✅ Pagamento aprovado!',
            'Olá, ' . e($order['customer_name']) . '! O pagamento do seu pedido foi confirmado. Em breve você receberá o aviso de envio com o código de rastreio.',
            $order, $items
        );
        self::send($order['customer_email'], 'Pagamento aprovado — pedido ' . $order['order_code'], $html);

        $admin = Setting::get('contact_email');
        if ($admin) {
            self::send($admin, '💰 Pagamento aprovado — pedido ' . $order['order_code'] . ' (' . money((float) $order['total']) . ')',
                self::orderHtml('Pagamento aprovado', 'O pagamento deste pedido foi confirmado pelo gateway. Já pode preparar o envio.', $order, $items, true));
        }
    }

    /** Monta o corpo HTML padrão dos e-mails de pedido. */
    private static function orderHtml(string $title, string $intro, array $order, array $items, bool $admin = false): string
    {
        $siteName = e(Setting::get('site_name', ''));
        $rows = '';
        foreach ($items as $item) {
            $rows .= '<tr><td style="padding:6px 10px;border-bottom:1px solid #e6e8ec">' . (int) $item['quantity'] . '× ' . e($item['title']) . '</td>'
                   . '<td style="padding:6px 10px;border-bottom:1px solid #e6e8ec;text-align:right">' . money((float) $item['line_total']) . '</td></tr>';
        }
        if ((float) $order['shipping'] > 0) {
            $rows .= '<tr><td style="padding:6px 10px">Frete</td><td style="padding:6px 10px;text-align:right">' . money((float) $order['shipping']) . '</td></tr>';
        }
        $address = e($order['address_street'] . ', ' . $order['address_number']
            . ($order['address_complement'] ? ' — ' . $order['address_complement'] : '')
            . ' · ' . $order['address_district'] . ' · ' . $order['address_city'] . '/' . $order['address_state']
            . ' · CEP ' . $order['address_zip']);
        $adminBlock = $admin
            ? '<p style="margin:14px 0 0;font-size:14px;color:#414751"><strong>Cliente:</strong> ' . e($order['customer_name'])
              . ' · ' . e($order['customer_email'])
              . ($order['customer_phone'] ? ' · WhatsApp ' . e($order['customer_phone']) : '') . '</p>'
            : '';

        return '<div style="font-family:Arial,Helvetica,sans-serif;max-width:560px;margin:0 auto;color:#1c2733">'
            . '<div style="background:#0A2540;color:#fff;padding:22px 26px;border-radius:10px 10px 0 0">'
            . '<h1 style="margin:0;font-size:20px">' . $title . '</h1>'
            . '<p style="margin:6px 0 0;color:#c8d3e0;font-size:13px">' . $siteName . ' — pedido <strong>' . e($order['order_code']) . '</strong></p></div>'
            . '<div style="border:1px solid #e6e8ec;border-top:none;padding:22px 26px;border-radius:0 0 10px 10px">'
            . '<p style="margin:0 0 16px;font-size:14px;line-height:1.6">' . $intro . '</p>'
            . '<table style="width:100%;border-collapse:collapse;font-size:14px">' . $rows
            . '<tr><td style="padding:10px;font-weight:bold">Total</td><td style="padding:10px;text-align:right;font-weight:bold">' . money((float) $order['total']) . '</td></tr></table>'
            . '<p style="margin:16px 0 0;font-size:13px;color:#5b6673"><strong>Entrega:</strong> ' . $address . '</p>'
            . $adminBlock
            . '<p style="margin:18px 0 0;font-size:12px;color:#8a93a0">Dúvidas? Fale conosco pelo WhatsApp ' . e(Setting::get('whatsapp_display', '')) . '.</p>'
            . '</div></div>';
    }
}

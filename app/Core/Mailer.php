<?php
namespace App\Core;

use App\Models\Book;
use App\Models\Setting;

/**
 * Envio de e-mails transacionais via mail() do PHP (padrão em hospedagem cPanel).
 *
 * Entregabilidade (para não cair em spam):
 *  1. Crie a caixa postal do remetente (ex.: contato@seudominio.com.br) no cPanel.
 *  2. No cPanel > "Capacidade de entrega de e-mail" (Email Deliverability),
 *     clique em "Reparar" para ativar SPF e DKIM do domínio.
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
            'Message-ID: <' . bin2hex(random_bytes(12)) . '@' . $host . '>',
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
            'Recebemos o seu pedido! 🙏',
            'Olá, <strong>' . e($order['customer_name']) . '</strong>! Seu pedido foi registrado com sucesso e já está sendo processado. Guarde o número <strong>' . e($order['order_code']) . '</strong> para acompanhamento.',
            $order, $items
        );
        self::send($order['customer_email'], 'Pedido ' . $order['order_code'] . ' recebido — ' . Setting::get('site_name', ''), $html);

        $admin = Setting::get('contact_email');
        if ($admin) {
            self::send($admin, '🛒 Novo pedido ' . $order['order_code'] . ' — ' . money((float) $order['total']),
                self::orderHtml('Novo pedido na loja', 'Um novo pedido foi realizado no site. Acesse o painel administrativo para ver os detalhes e preparar o envio.', $order, $items, true));
        }
    }

    /** E-mail ao cliente: pagamento aprovado. */
    public static function orderPaid(array $order, array $items): void
    {
        $html = self::orderHtml(
            'Pagamento aprovado! ✅',
            'Olá, <strong>' . e($order['customer_name']) . '</strong>! O pagamento do seu pedido <strong>' . e($order['order_code']) . '</strong> foi confirmado. Em breve você receberá o aviso de envio com o código de rastreio.',
            $order, $items
        );
        self::send($order['customer_email'], 'Pagamento aprovado — pedido ' . $order['order_code'], $html);

        $admin = Setting::get('contact_email');
        if ($admin) {
            self::send($admin, '💰 Pagamento aprovado — pedido ' . $order['order_code'] . ' (' . money((float) $order['total']) . ')',
                self::orderHtml('Pagamento aprovado', 'O pagamento deste pedido foi confirmado pelo gateway. Já pode preparar o envio.', $order, $items, true));
        }
    }

    /**
     * Template moderno dos e-mails de pedido: logo, capas dos livros,
     * autor, resumo de valores, endereço e mensagem de agradecimento.
     * (HTML de e-mail usa tabelas e estilos inline por compatibilidade.)
     */
    private static function orderHtml(string $title, string $intro, array $order, array $items, bool $admin = false): string
    {
        $siteName = e(Setting::get('site_name', ''));
        $logo     = e(Setting::get('site_logo', ''));
        $navy = '#0A2540'; $gold = '#C79A3C'; $sand = '#f6f4ef'; $line = '#e6e8ec'; $muted = '#5b6673';

        /* Linhas dos itens: capa + título + autor + quantidade + valor */
        $rows = '';
        foreach ($items as $item) {
            $book  = !empty($item['book_id']) ? Book::find((int) $item['book_id']) : null;
            $cover = $book && !empty($book['image'])
                ? '<img src="' . e($book['image']) . '" width="52" alt="Capa de ' . e($item['title']) . '" style="display:block;width:52px;height:auto;border-radius:5px;border:1px solid ' . $line . '">'
                : '<div style="width:52px;height:70px;background:' . $sand . ';border-radius:5px;border:1px solid ' . $line . '"></div>';
            $rows .= '<tr>'
                . '<td style="padding:12px 0;border-bottom:1px solid ' . $line . ';width:64px;vertical-align:top">' . $cover . '</td>'
                . '<td style="padding:12px 12px;border-bottom:1px solid ' . $line . ';vertical-align:top">'
                .   '<span style="font-size:15px;color:#1c2733;font-weight:bold">' . e($item['title']) . '</span><br>'
                .   '<span style="font-size:12px;color:' . $muted . '">por Pr. Elias José da Silva</span><br>'
                .   '<span style="font-size:12px;color:' . $muted . '">Quantidade: ' . (int) $item['quantity'] . '</span>'
                . '</td>'
                . '<td style="padding:12px 0;border-bottom:1px solid ' . $line . ';text-align:right;vertical-align:top;font-size:14px;color:#1c2733;white-space:nowrap"><strong>' . money((float) $item['line_total']) . '</strong></td>'
                . '</tr>';
        }

        $shippingRow = (float) $order['shipping'] > 0
            ? '<tr><td colspan="2" style="padding:8px 12px 0 0;text-align:right;font-size:13px;color:' . $muted . '">Frete</td>'
              . '<td style="padding:8px 0 0;text-align:right;font-size:13px;color:' . $muted . '">' . money((float) $order['shipping']) . '</td></tr>'
            : '';

        $address = e($order['address_street'] . ', ' . $order['address_number']
            . ($order['address_complement'] ? ' — ' . $order['address_complement'] : '')
            . ' · ' . $order['address_district'] . ' · ' . $order['address_city'] . '/' . $order['address_state']
            . ' · CEP ' . $order['address_zip']);

        $adminBlock = $admin
            ? '<table role="presentation" width="100%" style="margin-top:14px;background:' . $sand . ';border-radius:8px"><tr><td style="padding:14px 16px;font-size:13px;color:#414751">'
              . '<strong>Cliente:</strong> ' . e($order['customer_name']) . '<br>'
              . '<strong>E-mail:</strong> ' . e($order['customer_email'])
              . ($order['customer_phone'] ? '<br><strong>WhatsApp:</strong> ' . e($order['customer_phone']) : '')
              . '</td></tr></table>'
            : '';

        /* Bloco de agradecimento (somente para o cliente) */
        $thanks = $admin ? '' :
            '<table role="presentation" width="100%" style="margin-top:22px;background:' . $sand . ';border-left:4px solid ' . $gold . ';border-radius:0 8px 8px 0"><tr><td style="padding:18px 20px">'
            . '<p style="margin:0 0 8px;font-size:15px;color:' . $navy . ';font-weight:bold">Muito obrigado pela sua compra! 🙏</p>'
            . '<p style="margin:0 0 10px;font-size:13px;line-height:1.6;color:#414751">É uma alegria saber que a Palavra de Deus chegará até você por meio desta obra. '
            . 'Que esta leitura fortaleça a sua fé, edifique a sua vida e aprofunde a sua comunhão com o Senhor.</p>'
            . '<p style="margin:0;font-size:13px;font-style:italic;color:' . $muted . '">“Tudo quanto fizerdes, fazei-o de todo o coração, como ao Senhor.” — Colossenses 3:23</p>'
            . '<p style="margin:10px 0 0;font-size:13px;color:' . $navy . ';font-weight:bold">Pr. Elias José da Silva</p>'
            . '</td></tr></table>';

        $logoHtml = $logo
            ? '<img src="' . $logo . '" alt="' . $siteName . '" height="64" style="display:block;margin:0 auto 10px;height:64px;width:auto">'
            : '';

        return '<!DOCTYPE html><html lang="pt-BR"><body style="margin:0;padding:0;background:#eef1f5">'
            . '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#eef1f5;padding:24px 0"><tr><td align="center">'
            . '<table role="presentation" width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;font-family:Arial,Helvetica,sans-serif">'

            // Cabeçalho com logo
            . '<tr><td style="background:' . $navy . ';border-radius:12px 12px 0 0;padding:28px 30px;text-align:center">'
            . $logoHtml
            . '<p style="margin:0;color:#ffffff;font-size:18px;font-weight:bold">' . $siteName . '</p>'
            . '<p style="margin:4px 0 0;color:#c8d3e0;font-size:12px">' . e(Setting::get('site_tagline', '')) . '</p>'
            . '</td></tr>'

            // Faixa dourada
            . '<tr><td style="background:' . $gold . ';height:4px;font-size:0;line-height:0">&nbsp;</td></tr>'

            // Corpo
            . '<tr><td style="background:#ffffff;padding:28px 30px;border:1px solid ' . $line . ';border-top:none;border-radius:0 0 12px 12px">'
            . '<h1 style="margin:0 0 6px;font-size:21px;color:' . $navy . '">' . $title . '</h1>'
            . '<p style="margin:0 0 6px;font-size:12px;color:' . $muted . '">Pedido <strong>' . e($order['order_code']) . '</strong> · ' . date_br($order['created_at'] ?? date('Y-m-d H:i:s'), true) . '</p>'
            . '<p style="margin:14px 0 18px;font-size:14px;line-height:1.6;color:#414751">' . $intro . '</p>'

            . '<table role="presentation" width="100%" cellpadding="0" cellspacing="0">' . $rows . $shippingRow
            . '<tr><td colspan="2" style="padding:14px 12px 0 0;text-align:right;font-size:15px;color:' . $navy . ';font-weight:bold">Total</td>'
            . '<td style="padding:14px 0 0;text-align:right;font-size:18px;color:' . $navy . ';font-weight:bold;white-space:nowrap">' . money((float) $order['total']) . '</td></tr>'
            . '</table>'

            . '<p style="margin:18px 0 0;font-size:13px;color:' . $muted . '"><strong style="color:#414751">📦 Entrega:</strong> ' . $address . '</p>'
            . $adminBlock
            . $thanks

            // Rodapé
            . '<table role="presentation" width="100%" style="margin-top:24px;border-top:1px solid ' . $line . '"><tr><td style="padding:16px 0 0;text-align:center">'
            . '<p style="margin:0 0 4px;font-size:12px;color:' . $muted . '">Dúvidas sobre o pedido? Fale conosco pelo WhatsApp <strong>' . e(Setting::get('whatsapp_display', '')) . '</strong></p>'
            . '<p style="margin:0;font-size:11px;color:#9aa4b0">© ' . date('Y') . ' ' . $siteName . ' — <a href="' . e(base_url()) . '" style="color:' . $muted . '">' . e(preg_replace('#^https?://#', '', base_url())) . '</a></p>'
            . '</td></tr></table>'

            . '</td></tr></table>'
            . '</td></tr></table></body></html>';
    }
}

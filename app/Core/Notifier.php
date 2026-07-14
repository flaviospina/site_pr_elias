<?php
namespace App\Core;

use App\Models\Setting;

/**
 * Notificações push no celular via Telegram (grátis e confiável).
 * Cobre TODAS as vendas do site: Mercado Pago, PIX direto e WhatsApp.
 *
 * Como ativar (uma vez):
 *  1. No Telegram, fale com @BotFather → /newbot → copie o TOKEN.
 *  2. Fale com o seu novo bot (mande "oi") e depois com @userinfobot → copie o seu CHAT ID.
 *  3. Cole os dois em Painel → Notificações e salve (um teste é enviado na hora).
 */
class Notifier
{
    public static function enabled(): bool
    {
        return Setting::get('telegram_enabled') === '1'
            && Setting::get('telegram_bot_token')
            && Setting::get('telegram_chat_id');
    }

    /** Push: novo pedido registrado no site. */
    public static function orderReceived(array $order): void
    {
        if (!self::enabled()) return;
        self::telegram(
            "🛒 <b>Novo pedido no site!</b>\n\n"
            . 'Pedido: <b>' . self::esc($order['order_code']) . "</b>\n"
            . 'Cliente: ' . self::esc($order['customer_name']) . "\n"
            . 'Total: <b>' . self::esc(money((float) $order['total'])) . "</b>\n"
            . 'Pagamento: ' . self::esc(self::methodLabel($order['payment_method'])) . "\n"
            . ($order['customer_phone'] ? 'WhatsApp: ' . self::esc($order['customer_phone']) . "\n" : '')
            . "\n<i>Acesse o painel para ver os detalhes.</i>"
        );
    }

    /** Push: pagamento aprovado. */
    public static function orderPaid(array $order): void
    {
        if (!self::enabled()) return;
        self::telegram(
            "💰 <b>Pagamento APROVADO!</b> ✅\n\n"
            . 'Pedido: <b>' . self::esc($order['order_code']) . "</b>\n"
            . 'Cliente: ' . self::esc($order['customer_name']) . "\n"
            . 'Valor recebido: <b>' . self::esc(money((float) $order['total'])) . "</b>\n"
            . 'Forma: ' . self::esc(self::methodLabel($order['payment_method'])) . "\n\n"
            . '<i>Já pode preparar o envio.</i>'
        );
    }

    /** Envia uma mensagem de teste; retorna [ok, message]. */
    public static function test(): array
    {
        if (!Setting::get('telegram_bot_token') || !Setting::get('telegram_chat_id')) {
            return ['ok' => false, 'message' => 'Preencha o token do bot e o Chat ID antes de testar.'];
        }
        $ok = self::telegram("🔔 <b>Notificações ativadas!</b>\n\nO site " . self::esc(Setting::get('site_name', ''))
            . ' vai te avisar aqui a cada nova venda. 🙌');
        return $ok
            ? ['ok' => true, 'message' => 'Mensagem de teste enviada — confira o seu Telegram.']
            : ['ok' => false, 'message' => 'Não foi possível enviar. Confira o token do bot e o Chat ID (e se você já iniciou uma conversa com o bot).'];
    }

    private static function methodLabel(?string $method): string
    {
        return [
            'mercadopago' => 'Mercado Pago (cartão/PIX/boleto)',
            'pix'         => 'PIX direto',
            'pagseguro'   => 'PagBank',
            'paypal'      => 'PayPal',
            'whatsapp'    => 'Combinar pelo WhatsApp',
        ][$method] ?? ($method ?: '—');
    }

    private static function telegram(string $text): bool
    {
        $token = Setting::get('telegram_bot_token');
        $chat  = Setting::get('telegram_chat_id');
        if (!$token || !$chat) return false;

        $ch = curl_init('https://api.telegram.org/bot' . $token . '/sendMessage');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => http_build_query([
                'chat_id'    => $chat,
                'text'       => $text,
                'parse_mode' => 'HTML',
                'disable_web_page_preview' => true,
            ]),
            CURLOPT_TIMEOUT        => 12,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);
        $response = curl_exec($ch);
        $status   = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        curl_close($ch);
        return $response !== false && $status === 200;
    }

    private static function esc(string $s): string
    {
        return str_replace(['&', '<', '>'], ['&amp;', '&lt;', '&gt;'], $s);
    }
}

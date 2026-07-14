<?php
namespace App\Controllers\Admin;

use App\Models\Setting;

class SettingController extends AdminController
{
    /** Campos editáveis na tela "Configurações do site". */
    private const GENERAL_KEYS = [
        'site_name', 'site_tagline', 'site_logo', 'whatsapp', 'whatsapp_display',
        'contact_email', 'attendance_hours', 'instagram_url', 'facebook_url', 'youtube_url',
        'hero_title', 'hero_subtitle', 'hero_image', 'about_image', 'announcement_bar',
        'footer_verse', 'guarantee_days', 'shipping_flat', 'gtm_id', 'cookie_banner_enabled',
    ];

    /** Campos da tela "Pagamentos". */
    private const PAYMENT_KEYS = [
        'pay_mercadopago_enabled', 'pay_mercadopago_public_key', 'pay_mercadopago_access_token', 'pay_mercadopago_sandbox',
        'pay_pagseguro_enabled', 'pay_pagseguro_email', 'pay_pagseguro_token', 'pay_pagseguro_sandbox',
        'pay_paypal_enabled', 'pay_paypal_client_id', 'pay_paypal_secret', 'pay_paypal_sandbox',
        'pay_pix_enabled', 'pay_pix_key', 'pay_pix_key_type', 'pay_pix_holder',
        'pay_whatsapp_enabled',
    ];

    public function general(): void
    {
        $this->adminView('admin/settings/general', ['pageTitle' => 'Configurações do Site']);
    }

    public function saveGeneral(): void
    {
        $this->requireCsrf();
        $this->saveKeys(self::GENERAL_KEYS);
        flash('success', 'Configurações salvas com sucesso.');
        redirect('admin/configuracoes');
    }

    /** Campos da tela "Notificações". */
    private const NOTIFICATION_KEYS = [
        'telegram_enabled', 'telegram_bot_token', 'telegram_chat_id',
    ];

    public function notifications(): void
    {
        $this->adminView('admin/settings/notifications', ['pageTitle' => 'Notificações no Celular']);
    }

    public function saveNotifications(): void
    {
        $this->requireCsrf();
        $this->saveKeys(self::NOTIFICATION_KEYS);

        // Se ativado e preenchido, envia um teste imediato e reporta o resultado
        if (!empty($_POST['telegram_enabled']) && !empty($_POST['telegram_bot_token']) && !empty($_POST['telegram_chat_id'])) {
            $res = \App\Core\Notifier::test();
            flash($res['ok'] ? 'success' : 'error', 'Telegram: ' . $res['message']);
        } else {
            flash('success', 'Configurações de notificação salvas.');
        }
        redirect('admin/notificacoes');
    }

    public function payments(): void
    {
        $this->adminView('admin/settings/payments', ['pageTitle' => 'Gateways de Pagamento']);
    }

    public function savePayments(): void
    {
        $this->requireCsrf();
        $this->saveKeys(self::PAYMENT_KEYS);

        // Validação do Mercado Pago: testa o Access Token na própria API
        // e avisa se as credenciais não batem com o modo escolhido.
        $mpEnabled = !empty($_POST['pay_mercadopago_enabled']);
        $mpToken   = trim((string) ($_POST['pay_mercadopago_access_token'] ?? ''));
        $mpSandbox = !empty($_POST['pay_mercadopago_sandbox']);
        if ($mpEnabled && $mpToken !== '') {
            $check = \App\Core\Payment::mpValidateToken($mpToken);
            if (!$check['ok']) {
                flash('error', 'Mercado Pago: ' . $check['message']);
                redirect('admin/pagamentos');
            }
            if ($mpSandbox && !str_starts_with($mpToken, 'TEST-')) {
                flash('error', 'Mercado Pago: o "Modo teste" está marcado, mas o Access Token é de PRODUÇÃO (APP_USR-...). Para testar, cole as credenciais de TESTE (começam com TEST-) — ou desmarque o modo teste para vender de verdade. ' . $check['message']);
                redirect('admin/pagamentos');
            }
            if (!$mpSandbox && str_starts_with($mpToken, 'TEST-')) {
                flash('error', 'Mercado Pago: o modo teste está DESMARCADO, mas o Access Token é de TESTE (TEST-...). Cole as credenciais de PRODUÇÃO (APP_USR-...) para receber pagamentos reais. ' . $check['message']);
                redirect('admin/pagamentos');
            }
            flash('success', 'Configurações salvas. Mercado Pago conectado ✓ — ' . $check['message']);
            redirect('admin/pagamentos');
        }

        flash('success', 'Configurações de pagamento salvas com sucesso.');
        redirect('admin/pagamentos');
    }

    private function saveKeys(array $keys): void
    {
        $pairs = [];
        foreach ($keys as $key) {
            if (str_ends_with($key, '_enabled') || str_ends_with($key, '_sandbox')) {
                $pairs[$key] = !empty($_POST[$key]) ? '1' : '0';
            } elseif (isset($_POST[$key])) {
                $pairs[$key] = trim((string) $_POST[$key]);
            }
        }
        Setting::setMany($pairs);
    }

    /** Upload de imagens (capas de livros, fotos etc.) para public/uploads. */
    public function upload(): void
    {
        $this->requireCsrf();

        if (empty($_FILES['file']['tmp_name']) || !is_uploaded_file($_FILES['file']['tmp_name'])) {
            $this->json(['error' => 'Nenhum arquivo enviado.'], 400);
        }
        $file = $_FILES['file'];
        if ($file['size'] > 5 * 1024 * 1024) {
            $this->json(['error' => 'Arquivo muito grande (máx. 5 MB).'], 400);
        }

        $mime = mime_content_type($file['tmp_name']);
        $allowed = [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp',
            'image/gif'  => 'gif',
        ];
        if (!isset($allowed[$mime])) {
            $this->json(['error' => 'Formato inválido. Envie JPG, PNG, WEBP ou GIF.'], 400);
        }

        $name = date('Ymd-His') . '-' . bin2hex(random_bytes(4)) . '.' . $allowed[$mime];
        $dest = BASE_PATH . '/public/uploads/' . $name;
        if (!move_uploaded_file($file['tmp_name'], $dest)) {
            $this->json(['error' => 'Falha ao salvar o arquivo.'], 500);
        }

        $this->json(['url' => asset_url('uploads/' . $name)]);
    }
}

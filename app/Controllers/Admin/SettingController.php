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

    public function payments(): void
    {
        $this->adminView('admin/settings/payments', ['pageTitle' => 'Gateways de Pagamento']);
    }

    public function savePayments(): void
    {
        $this->requireCsrf();
        $this->saveKeys(self::PAYMENT_KEYS);
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

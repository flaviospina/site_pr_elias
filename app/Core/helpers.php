<?php
/** Funções auxiliares globais. */

/** Lê uma chave do config/config.php ("db", "db.host", "env"...). */
function config(string $key, $default = null)
{
    static $config = null;
    if ($config === null) {
        $config = require BASE_PATH . '/config/config.php';
    }
    $value = $config;
    foreach (explode('.', $key) as $part) {
        if (!is_array($value) || !array_key_exists($part, $value)) {
            return $default;
        }
        $value = $value[$part];
    }
    return $value;
}

/** Escapa texto para HTML (proteção XSS). Use em TODA saída de dado dinâmico. */
function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

/**
 * Caminho web base do site (ex.: "" na raiz, "/site_new" numa subpasta).
 * É usado tanto para gerar links quanto para o roteador remover o prefixo,
 * garantindo que os dois fiquem sempre em sincronia — inclusive quando o
 * site roda numa subpasta e o domínio NÃO aponta direto para /public.
 */
function base_path(): string
{
    static $bp = null;
    if ($bp === null) {
        $configured = (string) config('base_url');
        if ($configured !== '') {
            // Usa o caminho definido no config (mais confiável em subpastas)
            $bp = rtrim(parse_url($configured, PHP_URL_PATH) ?: '', '/');
        } else {
            // Auto: pasta do index.php, removendo "/public" do final quando o
            // domínio aponta para a raiz do projeto e o .htaccess encaminha.
            $dir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/')), '/');
            if (substr($dir, -7) === '/public') {
                $dir = substr($dir, 0, -7);
            }
            $bp = ($dir === '/' || $dir === '.') ? '' : $dir;
        }
    }
    return $bp;
}

/** URL base do site (auto-detectada ou definida no config). */
function base_url(string $path = ''): string
{
    static $base = null;
    if ($base === null) {
        $configured = rtrim((string) config('base_url'), '/');
        if ($configured !== '') {
            $base = $configured;
        } else {
            $https  = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
                || ($_SERVER['SERVER_PORT'] ?? null) == 443
                || ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https';
            $scheme = $https ? 'https' : 'http';
            $host   = $_SERVER['HTTP_HOST'] ?? 'localhost';
            $base   = $scheme . '://' . $host . base_path();
        }
    }
    return $base . '/' . ltrim($path, '/');
}

/**
 * URL de arquivos estáticos (css, js, uploads).
 * Quando o site é servido pelo index.php da RAIZ (subpasta em hospedagem
 * compartilhada), aponta para o caminho físico real "public/..." — que o
 * servidor entrega diretamente, sem depender de regra de rewrite.
 */
function asset_url(string $path): string
{
    $prefix = defined('PUBLIC_VIA_ROOT') ? 'public/' : '';
    $url    = base_url($prefix . ltrim($path, '/'));
    // Cache-buster automático: muda a URL quando o arquivo muda,
    // evitando que navegadores (principalmente no celular) usem CSS/JS antigos
    $file = BASE_PATH . '/public/' . ltrim($path, '/');
    if (is_file($file)) {
        $url .= '?v=' . filemtime($file);
    }
    return $url;
}

/**
 * Renderiza o banner do topo de uma página, combinando a config do admin
 * (tabela banners) com os padrões da própria página. Se não houver banner
 * configurado, mostra a faixa padrão com o título informado.
 *
 * @param array $defaults ['title','subtitle','kicker','trust'=>[]]
 */
function render_banner(string $location, array $defaults = []): void
{
    $saved = \App\Models\Banner::get($location);
    // Banner desativado explicitamente → usa só os padrões (faixa simples)
    $on = !$saved || (int) ($saved['enabled'] ?? 1) === 1;

    $b = [
        'image'        => $on ? ($saved['image'] ?? null ?: ($defaults['image'] ?? null)) : null,
        'title'        => ($on && !empty($saved['title'])) ? $saved['title'] : ($defaults['title'] ?? ''),
        'subtitle'     => ($on && !empty($saved['subtitle'])) ? $saved['subtitle'] : ($defaults['subtitle'] ?? ''),
        'kicker'       => ($on && !empty($saved['title'])) ? '' : ($defaults['kicker'] ?? ''),
        'button_text'  => $on ? ($saved['button_text'] ?? '') : '',
        'button_url'   => $on ? ($saved['button_url'] ?? '') : '',
        'button2_text' => $on ? ($saved['button2_text'] ?? '') : '',
        'button2_url'  => $on ? ($saved['button2_url'] ?? '') : '',
        'overlay'      => $saved['overlay'] ?? 70,
        'text_color'   => $saved['text_color'] ?? 'light',
        'align'        => $saved['align'] ?? 'center',
        'height'       => $saved['height'] ?? ($defaults['height'] ?? 'medium'),
        'trust'        => $defaults['trust'] ?? [],
    ];
    // Se a página trouxe kicker padrão e não há título custom, mantém o kicker
    if (!empty($defaults['kicker']) && (!$saved || empty($saved['title']))) {
        $b['kicker'] = $defaults['kicker'];
    }
    require BASE_PATH . '/app/Views/partials/banner.php';
}

/** Redireciona e encerra. */
function redirect(string $path): void
{
    header('Location: ' . (preg_match('#^https?://#', $path) ? $path : base_url($path)));
    exit;
}

/** Formata valor em reais. */
function money(float $value): string
{
    return 'R$ ' . number_format($value, 2, ',', '.');
}

/** Formata data (Y-m-d / datetime) para dd/mm/aaaa. */
function date_br(?string $date, bool $withTime = false): string
{
    if (!$date) return '';
    $ts = strtotime($date);
    return $ts ? date($withTime ? 'd/m/Y H:i' : 'd/m/Y', $ts) : '';
}

/** Nome do mês abreviado em português para uma data. */
function month_br(?string $date): string
{
    $months = [1=>'JAN',2=>'FEV',3=>'MAR',4=>'ABR',5=>'MAI',6=>'JUN',7=>'JUL',8=>'AGO',9=>'SET',10=>'OUT',11=>'NOV',12=>'DEZ'];
    $ts = $date ? strtotime($date) : false;
    return $ts ? $months[(int) date('n', $ts)] : '';
}

/** Gera slug a partir de um título (acentos → ascii). */
function slugify(string $text): string
{
    $map = ['á'=>'a','à'=>'a','ã'=>'a','â'=>'a','ä'=>'a','é'=>'e','ê'=>'e','è'=>'e','ë'=>'e','í'=>'i','ì'=>'i','î'=>'i','ï'=>'i','ó'=>'o','ò'=>'o','õ'=>'o','ô'=>'o','ö'=>'o','ú'=>'u','ù'=>'u','û'=>'u','ü'=>'u','ç'=>'c','ñ'=>'n'];
    $text = mb_strtolower(trim($text), 'UTF-8');
    $text = strtr($text, $map);
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    return trim($text, '-') ?: 'item';
}

/** Resume texto simples em N caracteres, sem cortar palavra. */
function excerpt_of(string $html, int $limit = 160): string
{
    $text = trim(preg_replace('/\s+/', ' ', strip_tags($html)));
    if (mb_strlen($text) <= $limit) return $text;
    $cut = mb_substr($text, 0, $limit);
    $pos = mb_strrpos($cut, ' ');
    return ($pos ? mb_substr($cut, 0, $pos) : $cut) . '…';
}

/** Mensagens flash de sessão. */
function flash(string $key, ?string $message = null)
{
    if ($message !== null) {
        $_SESSION['_flash'][$key] = $message;
        return null;
    }
    $msg = $_SESSION['_flash'][$key] ?? null;
    unset($_SESSION['_flash'][$key]);
    return $msg;
}

/** Valor antigo de formulário (repopular após erro). */
function old(string $key, string $default = ''): string
{
    return e($_SESSION['_old'][$key] ?? $default);
}

/** Sanitiza HTML vindo do editor do admin (permite tags de formatação comuns). */
function clean_html(string $html): string
{
    $allowed = '<p><br><strong><b><em><i><u><h2><h3><h4><ul><ol><li><blockquote><a><img><figure><figcaption><table><thead><tbody><tr><td><th><hr><span>';
    $html = strip_tags($html, $allowed);
    // Remove atributos de evento (onclick etc.) e javascript: em href/src
    $html = preg_replace('/\son\w+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $html);
    $html = preg_replace('/(href|src)\s*=\s*(["\']?)\s*javascript:[^"\'>\s]*/i', '$1=$2#', $html);
    return $html;
}

/** IP do visitante. */
function client_ip(): string
{
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

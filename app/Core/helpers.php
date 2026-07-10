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

/** URL base do site (auto-detectada ou definida no config). */
function base_url(string $path = ''): string
{
    static $base = null;
    if ($base === null) {
        $base = rtrim((string) config('base_url'), '/');
        if ($base === '') {
            $https  = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
                || ($_SERVER['SERVER_PORT'] ?? null) == 443
                || ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https';
            $scheme = $https ? 'https' : 'http';
            $host   = $_SERVER['HTTP_HOST'] ?? 'localhost';
            // Diretório onde o index.php está sendo servido (suporta subpasta)
            $dir  = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/')), '/');
            $base = $scheme . '://' . $host . $dir;
        }
    }
    return $base . '/' . ltrim($path, '/');
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
